<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Services\VoucherService;

class AdminController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Kelola Pembayaran (Verifikasi Bukti Bayar)
     */
    public function payments()
    {
        $orders = Order::with('user')->where('status', 'paid_verifying')->latest()->get();
        return view('admin.payments', compact('orders'));
    }

    /**
     * Setujui Pembayaran
     */
    public function approvePayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'processing']);

        return back()->with('success', 'Pembayaran Berhasil Diverifikasi! Status pesanan berubah menjadi Diproses.');
    }

    /**
     * Tolak Pembayaran
     */
    public function rejectPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'pending_payment']); // Kembalikan agar user upload ulang bukti yang benar

        return back()->with('error', 'Pembayaran Ditolak. Harap pembeli mengunggah bukti yang valid.');
    }

    public function products()
    {
        $products = Product::with('seller', 'category')->latest()->get();
        return view('admin.products', compact('products'));
    }

    public function destroyProduct($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Produk dihapus admin');
    }

    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User dihapus');
    }

    public function vouchers()
    {
        $vouchers = $this->voucherService->getAll();
        return view('admin.vouchers', compact('vouchers'));
    }

    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers',
            'discount_amount' => 'required|numeric',
            'valid_until' => 'nullable|date'
        ]);
        $this->voucherService->createVoucher($request->user()->id, $validated);
        return back()->with('success', 'Voucher dibuat');
    }
}
