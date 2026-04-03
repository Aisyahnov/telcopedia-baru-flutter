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

    public function products()
    {
        return response()->json(['data' => Product::with('seller', 'category')->latest()->get()]);
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
}
