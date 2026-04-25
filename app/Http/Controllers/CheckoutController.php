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
        $productId = $request->query('product_id');
        $cartItemIds = $request->query('cart_item_ids');
        $cart = $this->cartService->getUserCart($request->user()->id);
        
        if ($productId) {
            // Filter hanya item yang dipilih (Buy Now)
            $items = $cart->items->where('product_id', $productId);
            if ($items->isEmpty()) return redirect()->route('home');

            $subtotal = $items->sum(fn($i) => $i->quantity * $i->product->price);
            $adminFee = $subtotal * 0.05;
            $discount = 0; 
            $total = $subtotal + $adminFee;
        } elseif ($cartItemIds) {
            // Filter item terpilih dari keranjang
            $selectedIds = explode(',', $cartItemIds);
            $items = $cart->items->whereIn('id', $selectedIds);
            
            if ($items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Silakan pilih produk terlebih dahulu.');
            }
            
            $subtotal = $items->sum(fn($i) => $i->quantity * $i->product->price);
            $adminFee = $subtotal * 0.05;
            $discount = $cart->voucher ? $this->cartService->calculateDiscount($subtotal, $cart->voucher) : 0;
            $total = $subtotal + $adminFee - $discount;
        } else {
            // Checkout seluruh isi keranjang
            $cartDetails = $this->cartService->getCartTotal($request->user()->id);
            $items = $cart->items;
            $subtotal = $cartDetails['subtotal'];
            $adminFee = $cartDetails['admin_fee'];
            $discount = $cartDetails['discount'];
            $total = $cartDetails['total'];
        }

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'admin_fee' => $adminFee,
            'discount' => $discount,
            'total' => $total,
            'userAddress' => $request->user()->address,
            'buyNowProductId' => $productId,
            'cartItemIds' => $cartItemIds
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|min:10',
            'payment_method' => 'required|in:transfer,cod',
        ]);

        try {
            $order = $this->checkoutService->processCheckout(
                $request->user()->id, 
                $request->input('shipping_address'),
                $request->input('payment_method'),
                $request->input('buy_now_product_id'),
                $request->input('cart_item_ids')
            );
            if ($request->input('payment_method') === 'cod') {
                return redirect('/orders')->with('success', 'Pesanan COD berhasil dibuat! Silakan hubungi seller untuk janji temu.');
            }

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
