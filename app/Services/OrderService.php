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

    public function completeOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'completed';
        $order->save();
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
}
