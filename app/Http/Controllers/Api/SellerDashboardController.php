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
                'total_reviews' => \App\Models\Review::where('seller_id', $sellerId)->count(),
                'avg_product_rating' => \App\Models\Review::where('seller_id', $sellerId)->avg('rating') ?? 0,
                'avg_seller_rating' => \App\Models\Review::where('seller_id', $sellerId)->avg('seller_rating') ?? 0,
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

    public function reviews(Request $request)
    {
        $sellerId = $request->user()->id;
        $reviews = \App\Models\Review::where('seller_id', $sellerId)->with(['user', 'product'])->latest()->get();

        return response()->json(['data' => $reviews]);
    }

    public function penarikan(Request $request)
    {
        $sellerId = $request->user()->id;
        $penarikan = \App\Models\PenarikanDana::where('user_id', $sellerId)->latest()->get();

        return response()->json(['data' => $penarikan]);
    }

    public function requestPenarikan(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        $user = $request->user();

        if ($user->saldo < $request->amount) {
            return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
        }

        $penarikan = \App\Models\PenarikanDana::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'pending',
        ]);

        $user->decrement('saldo', $request->amount);

        return response()->json([
            'message' => 'Penarikan dana berhasil diajukan',
            'data' => $penarikan
        ], 201);
    }
}
