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
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'pending_payments' => Order::where('status', 'paid_verifying')->count(),
            'total_completed_orders' => Order::where('status', 'completed')->count(),
            'total_revenue' => Order::where('status', 'completed')->get()->sum(function($order) {
                // Admin fee is 5% of subtotal
                $subtotal = $order->items->sum(fn($i) => $i->price * $i->quantity);
                return $subtotal * 0.05;
            }),
            'pending_withdrawals' => \App\Models\PenarikanDana::where('status', 'pending')->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function approveProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 'approved';
        $product->save();

        // Notify Seller
        $product->seller->notify(new \App\Notifications\SystemNotification(
            'Produk Disetujui',
            "Produk Anda '{$product->name}' telah disetujui dan sekarang tayang.",
            'product'
        ));

        return back()->with('success', 'Produk berhasil disetujui untuk tayang.');
    }

    public function rejectProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 'rejected';
        $product->save();

        // Notify Seller
        $product->seller->notify(new \App\Notifications\SystemNotification(
            'Produk Ditolak',
            "Maaf, produk '{$product->name}' ditolak oleh admin.",
            'product'
        ));

        return back()->with('success', 'Produk telah ditolak.');
    }

    /**
     * Monitoring Pembayaran (Admin hanya monitoring)
     */
    public function payments()
    {
        // Admin memantau semua order yang masuk untuk melihat perputaran uang dan biaya admin
        $orders = Order::with('user')->latest()->get();
        return view('admin.payments', compact('orders'));
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
            'min_spend' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date'
        ]);
        $this->voucherService->createVoucher($request->user()->id, $validated);

        // Notify all buyers about the new voucher
        \App\Models\User::where('role', 'buyer')->get()->each(function($user) use ($validated) {
            $user->notify(new \App\Notifications\SystemNotification(
                'Voucher Spesial Baru! 🎉',
                "Gunakan kode '{$validated['code']}' untuk mendapatkan potongan harga Rp " . number_format($validated['discount_amount'], 0, ',', '.'),
                'info',
                '/vouchers'
            ));
        });

        return back()->with('success', 'Voucher dibuat dan pengguna telah dinotifikasi');
    }

    public function updateVoucher(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code,'.$id,
            'discount_amount' => 'required|numeric',
            'min_spend' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date'
        ]);
        $this->voucherService->updateVoucher($id, $validated);
        return back()->with('success', 'Voucher berhasil diperbarui');
    }

    public function destroyVoucher($id)
    {
        $this->voucherService->deleteVoucher($id);
        return back()->with('success', 'Voucher berhasil dihapus');
    }
}
