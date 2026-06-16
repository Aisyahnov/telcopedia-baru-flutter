<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', $request->user()->id);
        $filter = $request->query('filter', 'all');

        switch ($filter) {
            case 'pending':
                $query->where('status', 'pending_payment');
                break;
            case 'paid':
                $query->where('status', 'paid_verifying');
                break;
            case 'processing':
                $query->where('status', 'processing');
                break;
            case 'shipped':
                $query->where('status', 'shipped');
                break;
            case 'completed':
                $query->where('status', 'completed');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled');
                break;
            case 'returned':
                $query->has('returns');
                break;
            case 'reviewed':
                $query->has('reviews');
                break;
        }

        if ($keyword = $request->query('keyword')) {
            $query->where(function($q) use ($keyword) {
                // If user types "ORD-12", remove the prefix for ID search
                $cleanId = str_ireplace('ORD-', '', $keyword);
                
                $q->where('id', 'LIKE', "%{$cleanId}%")
                  ->orWhereHas('items.product', function($q2) use ($keyword) {
                      $q2->where('name', 'LIKE', "%{$keyword}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
                        ->paginate(5)
                        ->appends(['filter' => $filter, 'keyword' => $request->query('keyword')]);

        return view('orders.index', compact('orders', 'filter'));
    }

    public function complete(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        
        if ($order->status !== 'shipped' && $order->status !== 'processing') {
            return back()->with('error', 'Status pesanan tidak dapat diselesaikan saat ini.');
        }

        $order->status = 'completed';
        $order->save();

        // Distribute funds to sellers
        foreach ($order->items as $item) {
            $seller = $item->product->seller;
            if ($seller) {
                // Seller gets the item subtotal (price * quantity)
                $amount = $item->price * $item->quantity;
                $seller->increment('balance', $amount);
            }
        }

        return back()->with('success', 'Pesanan telah diterima. Terima kasih! Jangan lupa berikan ulasan produk.');
    }
    public function cancel(Request $request, $id)
    {
        try {
            $service = app(\App\Services\OrderService::class);
            $service->cancelOrder($id, $request->user()->id);
            return back()->with('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
