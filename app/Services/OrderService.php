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

    public function verifyPayment($orderId) // Admin action
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'processing';
        $order->save();
        return $order;
    }

    public function updateTracking($orderId, $trackingNumber) // Seller action
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'shipped';
        $order->tracking_number = $trackingNumber;
        $order->save();
        return $order;
    }
}
