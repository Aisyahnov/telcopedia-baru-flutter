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
        return view('seller.dashboard', compact('totalProducts'));
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
}
