<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        // Jika dipanggil dari prefix /seller, maka ambil pesanan seller
        if ($request->is('api/seller/*')) {
            return $this->sellerOrders($request);
        }
        return $this->buyerOrders($request);
    }

    public function buyerOrders(Request $request)
    {
        return response()->json(['data' => $this->orderService->getBuyerOrders($request->user()->id)]);
    }

    public function sellerOrders(Request $request)
    {
        return response()->json(['data' => $this->orderService->getSellerOrders($request->user()->id)]);
    }

    public function updateTracking(Request $request, $orderId)
    {
        $request->validate(['tracking_number' => 'required|string']);
        $order = \App\Models\Order::findOrFail($orderId);
        $order->update([
            'tracking_number' => $request->tracking_number,
            'status' => 'processing' // Otomatis pindah status jika diupdate resi
        ]);
        return response()->json(['data' => $order, 'message' => 'Tracking number updated']);
    }

    public function show($id)
    {
        return response()->json(['data' => $this->orderService->getOrderDetails($id)]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $order = \App\Models\Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        
        return response()->json(['data' => $order, 'message' => 'Order status updated']);
    }

    public function approvePayment($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->update(['status' => 'processing']);
        return response()->json(['data' => $order, 'message' => 'Payment approved and order is being processed']);
    }

    public function rejectPayment(Request $request, $id)
    {
        try {
            $order = $this->orderService->cancelOrder($id, $request->user()->id);
            return response()->json(['data' => $order, 'message' => 'Payment rejected and order cancelled, stock returned.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function completeOrder(Request $request, $orderId)
    {
        $order = $this->orderService->completeOrder($orderId, $request->user()->id);
        return response()->json(['data' => $order, 'message' => 'Order completed']);
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'media' => 'nullable|file|max:10240',
        ]);

        $path = $request->hasFile('media') ? $request->file('media')->store('reviews', 'public') : null;
        
        // Prevent duplicate review
        $existingReview = \App\Models\Review::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Anda sudah memberikan ulasan untuk produk ini pada pesanan yang sama.'], 400);
        }

        // Prevent review if product has been returned
        $existingReturn = \App\Models\ProductReturn::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReturn) {
            return response()->json(['message' => 'Anda tidak dapat memberikan ulasan karena Anda telah mengajukan pengembalian untuk produk ini.'], 400);
        }

        $review = \App\Models\Review::create([
            'user_id' => $request->user()->id,
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'media' => $path,
        ]);

        return response()->json(['data' => $review, 'message' => 'Review submitted']);
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'tipe_retur' => 'required|in:tukar_barang,kembali_dana',
            'reason' => 'required|string',
            'media' => 'nullable|file|max:10240',
        ]);

        $path = $request->hasFile('media') ? $request->file('media')->store('returns', 'public') : null;

        $return = \App\Models\ProductReturn::create([
            'user_id' => $request->user()->id,
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'tipe_retur' => $request->tipe_retur,
            'reason' => $request->reason,
            'media' => $path,
            'status' => 'pending',
        ]);

        return response()->json(['data' => $return, 'message' => 'Return requested']);
    }

    public function sellerReturns(Request $request)
    {
        $returns = \App\Models\ProductReturn::whereHas('product', function($q) use ($request) {
            $q->where('seller_id', $request->user()->id); 
        })->with(['user', 'order', 'product'])->latest()->get();

        return response()->json(['data' => $returns]);
    }

    public function approveReturn($id)
    {
        $return = \App\Models\ProductReturn::with(['order.items.product', 'user'])->findOrFail($id);
        
        if ($return->status !== 'pending') {
            return response()->json(['message' => 'Status retur tidak dapat diubah.'], 400);
        }

        $return->update(['status' => 'approved']);
        
        if ($return->order->status !== 'returned') {
            $return->order->update(['status' => 'returned']);
            
            if ($return->tipe_retur === 'tukar_barang') {
                // Kembalikan stok produk jika tipe tukar barang (karena pembeli memulangkan fisik barang)
                foreach ($return->order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }
        }

        if ($return->tipe_retur === 'kembali_dana') {
            $orderItem = $return->order->items->where('product_id', $return->product_id)->first();
            if ($orderItem) {
                $refundAmount = $orderItem->price * $orderItem->quantity;
                
                // Refund ke pembeli
                $return->user->increment('saldo', $refundAmount);

                // Jika pesanan sudah selesai, tarik kembali saldo penjual
                if ($return->order->status === 'completed') {
                    $seller = $orderItem->product->seller ?? \App\Models\User::find($orderItem->product->seller_id);
                    if ($seller) {
                        $seller->decrement('saldo', $refundAmount);
                    }
                }
            }
        }
        
        return response()->json(['data' => $return, 'message' => 'Return request approved']);
    }

    public function cancel($id, Request $request)
    {
        try {
            $order = $this->orderService->cancelOrder($id, $request->user()->id);
            return response()->json(['data' => $order, 'message' => 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function rejectReturn($id)
    {
        $return = \App\Models\ProductReturn::findOrFail($id);
        $return->update(['status' => 'rejected']);
        return response()->json(['data' => $return, 'message' => 'Return request rejected']);
    }
}
