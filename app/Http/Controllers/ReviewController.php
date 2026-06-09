<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'seller_rating' => 'nullable|integer|min:1|max:5',
            'seller_comment' => 'nullable|string|max:500',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480'
        ]);

        $order = Order::findOrFail($request->order_id);

        // Ensure the order belongs to the user and is completed
        if ($order->user_id !== Auth::id() || $order->status !== 'completed') {
            return back()->with('error', 'Pesanan tidak valid untuk diulas.');
        }

        // Ensure this exact product is in the order
        if (!$order->items()->where('product_id', $request->product_id)->exists()) {
            return back()->with('error', 'Produk tidak ditemukan dalam pesanan ini.');
        }

        // Prevent duplicate review for the same order & product
        $existingReview = Review::where('order_id', $order->id)
            ->where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini pada pesanan yang sama.');
        }

        // Prevent review if product has been returned
        $existingReturn = \App\Models\ProductReturn::where('order_id', $order->id)
            ->where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReturn) {
            return back()->with('error', 'Anda tidak dapat memberikan ulasan karena Anda telah mengajukan pengembalian untuk produk ini.');
        }

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('reviews', 'public');
        }

        Review::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'product_id' => $request->product_id,
            'seller_id' => Product::find($request->product_id)->seller_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'seller_rating' => $request->seller_rating ?? $request->rating,
            'seller_comment' => $request->seller_comment ?? $request->comment,
            'media' => $mediaPath
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil disimpan.');
    }
}
