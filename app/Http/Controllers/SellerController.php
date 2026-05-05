<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\Product;

class SellerController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function dashboard(Request $request)
    {
        $sellerId = $request->user()->id;
        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $avgProductRating = \App\Models\Review::where('seller_id', $sellerId)->avg('rating') ?? 0;
        $avgSellerRating = \App\Models\Review::where('seller_id', $sellerId)->avg('seller_rating') ?? 0;
        $totalReviews = \App\Models\Review::where('seller_id', $sellerId)->count();

        return view('seller.dashboard', compact('totalProducts', 'avgProductRating', 'avgSellerRating', 'totalReviews'));
    }

    public function orders(Request $request)
    {
        $orders = $this->orderService->getSellerOrders($request->user()->id);
        return view('seller.orders', compact('orders'));
    }

    public function updateTracking(Request $request, $orderId)
    {
        $request->validate(['tracking_number' => 'required|string']);
        $this->orderService->updateTracking($orderId, $request->tracking_number);
        return back()->with('success', 'Tracking number diupdate');
    }

    public function approvePayment($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        // Pastikan order milik seller ini
        $isOwner = $order->items()->whereHas('product', function($q) {
            $q->where('seller_id', auth()->id());
        })->exists();

        if (!$isOwner) abort(403);

        $order->status = 'processing';
        $order->save();

        return back()->with('success', 'Pembayaran disetujui, order masuk tahap proses.');
    }

    public function rejectPayment($id)
    {
        try {
            $this->orderService->cancelOrder($id, auth()->id());
            return back()->with('success', 'Pembayaran ditolak dan pesanan telah dibatalkan. Stok produk dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
