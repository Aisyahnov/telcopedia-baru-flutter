<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function getBuyerOrders($userId)
    {
        return Order::where('user_id', $userId)->with('items.product')->latest()->get();
    }

    public function getSellerOrders($sellerId)
    {
        return Order::whereHas('items.product', function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })->with('items.product')->latest()->get();
    }

    public function uploadPaymentProof($orderId, $userId, $proofPath)
    {
        $order = Order::where('id', $orderId)->where('user_id', $userId)->firstOrFail();
        $order->payment_proof = $proofPath;
        $order->status = 'paid_verifying';
        $order->save();
        return $order;
    }

    public function getOrderDetails($orderId)
    {
        return Order::with(['items.product', 'reviews', 'returns'])->findOrFail($orderId);
    }

    public function completeOrder($orderId, $buyerId)
    {
        $order = Order::where('id', $orderId)->where('user_id', $buyerId)->firstOrFail();
        
        if ($order->status !== 'processing' && $order->status !== 'delivered') {
             throw new \Exception('Pesanan belum bisa diselesaikan');
        }

        $order->status = 'completed';
        $order->save();

        // Transfer dana pesanan ke saldo penjual
        $firstItem = $order->items()->with('product')->first();
        if ($firstItem && $firstItem->product) {
            \App\Models\User::where('id', $firstItem->product->seller_id)
                ->increment('saldo', $order->total_amount);
        }

        return $order;
    }

    public function storeReturn($userId, $orderId, $productId, $reason, $mediaPath = null)
    {
        return \App\Models\ProductReturn::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'product_id' => $productId,
            'reason' => $reason,
            'media' => $mediaPath,
            'status' => 'pending'
        ]);
    }

    public function storeReview($userId, $orderId, $productId, $rating, $comment = null, $mediaPath = null)
    {
        return \App\Models\Review::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'product_id' => $productId,
            'rating' => $rating,
            'comment' => $comment,
            'media' => $mediaPath
        ]);
    }

    public function cancelOrder($orderId, $userId)
    {
        $order = Order::where('id', $orderId)
                      ->where(function($q) use ($userId) {
                          $q->where('user_id', $userId) // Buyer
                            ->orWhereHas('items.product', function($sq) use ($userId) {
                                $sq->where('seller_id', $userId); // Seller
                            });
                      })->firstOrFail();

        if ($order->status === 'cancelled' || $order->status === 'completed' || $order->status === 'returned') {
            throw new \Exception('Pesanan dengan status ' . $order->status . ' tidak dapat dibatalkan.');
        }

        // Kembalikan stok produk
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->status = 'cancelled';
        $order->save();

        return $order;
    }
}
