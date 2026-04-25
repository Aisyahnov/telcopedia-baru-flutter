<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductReturn;
use Illuminate\Support\Facades\Auth;

class ProductReturnController extends Controller
{
    // Buyer: Ajukan Retur
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'reason' => 'required|string|max:1000',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480'
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        // Check if return already requested
        $existing = ProductReturn::where('order_id', $order->id)
            ->where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah mengajukan retur untuk produk ini.');
        }

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('returns', 'public');
        }

        ProductReturn::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'product_id' => $request->product_id,
            'reason' => $request->reason,
            'status' => 'pending',
            'media' => $mediaPath
        ]);

        return back()->with('success', 'Pengajuan retur berhasil dikirim. Menunggu konfirmasi penjual.');
    }

    // Seller: Halaman Daftar Retur Minta di-Approve
    public function indexSeller(Request $request)
    {
        $sellerId = Auth::id();
        
        $returns = ProductReturn::whereHas('product', function($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })->with(['user', 'order', 'product'])->latest()->get();

        return view('seller.returns.index', compact('returns'));
    }

    // Seller: Setujui Retur
    public function approve($id)
    {
        $sellerId = Auth::id();
        $return = ProductReturn::whereHas('product', function($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })->findOrFail($id);

        $return->status = 'approved';
        $return->save();

        return back()->with('success', 'Retur disetujui.');
    }

    // Seller: Tolak Retur
    public function reject($id)
    {
        $sellerId = Auth::id();
        $return = ProductReturn::whereHas('product', function($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })->findOrFail($id);

        $return->status = 'rejected';
        $return->save();

        return back()->with('success', 'Retur ditolak.');
    }
}
