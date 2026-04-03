<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\CartService;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $orderService;
    protected $cartService;

    public function __construct(CheckoutService $checkoutService, OrderService $orderService, CartService $cartService)
    {
        $this->checkoutService = $checkoutService;
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $items = $this->cartService->getUserCart($request->user()->id);
        $cartDetails = $this->cartService->getCartTotal($request->user()->id);
        
        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $cartDetails['subtotal'],
            'admin_fee' => $cartDetails['admin_fee'],
            'discount' => $cartDetails['discount'],
            'total' => $cartDetails['total']
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|min:10',
        ]);

        try {
            $order = $this->checkoutService->processCheckout($request->user()->id, $request->input('shipping_address'));
            return redirect()->route('checkout.upload', $order->id)->with('success', 'Pesanan dibuat! Silakan upload bukti pembayaran.');
        } catch (\Exception $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }
    }

    public function showUpload($orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        return view('checkout.upload', compact('order'));
    }

    public function uploadBukti(Request $request, $orderId)
    {
        $request->validate(['payment_proof' => 'required|image|max:10240']);
        $path = $request->file('payment_proof')->store('proofs', 'public');
        $this->orderService->uploadPaymentProof($orderId, $request->user()->id, $path);
        
        // Gunakan redirect manual ke path /orders untuk stabilitas maksimal di dev server
        return redirect('/orders')->with('success', 'Terima kasih! Bukti pembayaran Anda telah kami terima dan sedang diverifikasi oleh tim Telcopedia.');
    }
}
