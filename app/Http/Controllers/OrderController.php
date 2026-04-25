<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        return view('orders.index', compact('orders'));
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
}
