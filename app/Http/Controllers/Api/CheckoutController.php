<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $orderService;

    public function __construct(CheckoutService $checkoutService, OrderService $orderService)
    {
        $this->checkoutService = $checkoutService;
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        // Meringkas dan menampilkan data siap checkout
        return response()->json(['message' => 'Checkout page data']);
    }

    public function save(Request $request)
    {
        try {
            $voucherId = $request->input('voucher_id');
            $order = $this->checkoutService->processCheckout($request->user()->id, $voucherId);
            return response()->json(['data' => $order, 'message' => 'Checkout success'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function uploadBukti(Request $request, $orderId)
    {
        $request->validate(['payment_proof' => 'required|image|max:2048']);
        $path = $request->file('payment_proof')->store('proofs', 'public');

        $order = $this->orderService->uploadPaymentProof($orderId, $request->user()->id, $path);
        return response()->json(['data' => $order, 'message' => 'Payment proof uploaded']);
    }

    public function applyVoucher(Request $request)
    {
        return response()->json(['message' => 'Voucher check at checkout']);
    }
}
