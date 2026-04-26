<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Http\Request;

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
            'pending_payments' => \App\Models\Order::where('status', 'paid_verifying')->count(),
            'total_completed_orders' => \App\Models\Order::where('status', 'completed')->count(),
            'total_revenue' => \App\Models\Order::where('status', 'completed')->get()->sum(function($order) {
                $subtotal = $order->items->sum(fn($i) => $i->price * $i->quantity);
                return $subtotal * 0.05;
            }),
            'pending_withdrawals' => \App\Models\Withdrawal::where('status', 'pending')->count(),
        ];
        return response()->json(['data' => $stats]);
    }

    public function products()
    {
        return response()->json(['data' => Product::with('seller', 'category')->latest()->get()]);
    }

    public function approveProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 'approved';
        $product->save();
        return response()->json(['message' => 'Product approved']);
    }

    public function rejectProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 'rejected';
        $product->save();
        return response()->json(['message' => 'Product rejected']);
    }

    public function destroyProduct($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted by admin']);
    }

    public function users()
    {
        return response()->json(['data' => User::latest()->get()]);
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted by admin']);
    }

    public function vouchers()
    {
        return response()->json(['data' => $this->voucherService->getAll()]);
    }

    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers',
            'discount_amount' => 'required|numeric',
            'valid_until' => 'nullable|date'
        ]);

        $voucher = $this->voucherService->createVoucher($request->user()->id, $validated);
        return response()->json(['data' => $voucher], 201);
    }

    public function destroyVoucher($id)
    {
        $this->voucherService->deleteVoucher($id);
        return response()->json(['message' => 'Voucher deleted']);
    }

    public function payments()
    {
        $orders = \App\Models\Order::with('user')->latest()->get();
        return response()->json(['data' => $orders]);
    }

    public function withdrawals()
    {
        $withdrawals = \App\Models\Withdrawal::with('user')->latest()->get();
        return response()->json(['data' => $withdrawals]);
    }

    public function approveWithdrawal($id)
    {
        $withdrawal = \App\Models\Withdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') return response()->json(['message' => 'Already processed'], 400);

        $withdrawal->status = 'approved';
        $withdrawal->save();
        return response()->json(['message' => 'Withdrawal approved']);
    }

    public function rejectWithdrawal($id)
    {
        $withdrawal = \App\Models\Withdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') return response()->json(['message' => 'Already processed'], 400);

        $withdrawal->status = 'rejected';
        $withdrawal->save();
        $withdrawal->user->increment('balance', $withdrawal->amount);
        return response()->json(['message' => 'Withdrawal rejected']);
    }
}
