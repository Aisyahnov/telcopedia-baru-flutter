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
        $order = $this->orderService->updateTracking($orderId, $request->tracking_number);
        return response()->json(['data' => $order, 'message' => 'Tracking number updated']);
    }

    public function verifyPayment($orderId)
    {
        $order = $this->orderService->verifyPayment($orderId);
        return response()->json(['data' => $order, 'message' => 'Payment verified']);
    }
}
