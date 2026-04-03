<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Chat;

class SellerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = $request->user()->id;
        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $totalOrders = Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->count();

        return response()->json([
            'data' => [
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
            ]
        ]);
    }

    public function revenue(Request $request)
    {
        $sellerId = $request->user()->id;
        $revenue = Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->where('status', 'completed')->sum('total_amount');

        return response()->json(['data' => ['revenue' => $revenue]]);
    }

    public function productStats(Request $request)
    {
        $sellerId = $request->user()->id;
        $stats = Product::where('seller_id', $sellerId)->select('id', 'name', 'stock', 'price')->get();
        return response()->json(['data' => $stats]);
    }

    public function chatList(Request $request)
    {
        $sellerId = $request->user()->id;
        $chats = Chat::where('user1_id', $sellerId)->orWhere('user2_id', $sellerId)
                     ->with(['user1', 'user2', 'messages' => function($q) { $q->latest()->limit(1); }])
                     ->get();
        return response()->json(['data' => $chats]);
    }
}
