<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function processCheckout($userId, $shippingAddress, $paymentMethod = 'transfer', $productId = null, $cartItemIds = null, $voucherCode = null)
    {
        return DB::transaction(function () use ($userId, $shippingAddress, $paymentMethod, $productId, $cartItemIds, $voucherCode) {
            $cart = Cart::where('user_id', $userId)->with('items.product')->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                throw new \Exception("Keranjang belanja kosong.");
            }

            $items = $cart->items;
            if ($productId) {
                $items = $items->where('product_id', $productId);
            } elseif ($cartItemIds) {
                $ids = is_array($cartItemIds) ? $cartItemIds : explode(',', $cartItemIds);
                $items = $items->whereIn('id', $ids);
            }

            if ($items->isEmpty()) {
                throw new \Exception("Item tidak ditemukan.");
            }

            // Hitung total (Bisa full cart atau partial)
            if ($productId || $cartItemIds) {
                $subtotal = $items->sum(fn($i) => $i->quantity * $i->product->price);
                $discount = 0;
                $voucherId = null;

                $voucher = null;
                if ($voucherCode) {
                    $voucher = Voucher::where('code', $voucherCode)->first();
                } elseif ($cart->voucher) {
                    $voucher = $cart->voucher;
                }

                if ($voucher) {
                    $discount = min($voucher->discount_amount, $subtotal);
                    $voucherId = $voucher->id;
                }

                $adminFee = $subtotal * 0.05;
                $total = $subtotal + $adminFee - $discount;
            } else {
                $cartDetails = $this->cartService->getCartTotal($userId);
                $subtotal = $cartDetails['subtotal'];
                $discount = $cartDetails['discount'];
                $adminFee = $cartDetails['admin_fee'];
                $total = $cartDetails['total'];
                $voucherId = $cart->voucher_id;
            }
            
            $status = $paymentMethod === 'cod' ? 'paid_verifying' : 'pending_payment';

            $order = Order::create([
                'user_id' => $userId,
                'shipping_address' => $shippingAddress,
                'payment_method' => $paymentMethod,
                'voucher_id' => $voucherId,
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'admin_fee' => $adminFee,
                'total_amount' => $total,
                'status' => $status,
            ]);

            foreach ($items as $item) {
                $item->product->decrement('stock', $item->quantity);
                
                // Jika stok habis, otomatis tidak aktif
                if ($item->product->fresh()->stock <= 0) {
                    $item->product->update(['status' => 'inactive']);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Hapus item yang di-checkout saja
            if ($productId) {
                $cart->items()->where('product_id', $productId)->delete();
            } elseif ($cartItemIds) {
                $ids = is_array($cartItemIds) ? $cartItemIds : explode(',', $cartItemIds);
                $cart->items()->whereIn('id', $ids)->delete();
                // Clear voucher if cart is empty
                if ($cart->items()->count() === 0) {
                    $cart->update(['voucher_id' => null]);
                }
            } else {
                $cart->update(['voucher_id' => null]);
                $cart->items()->delete();
            }

            // Notify Sellers
            $sellers = $items->map(fn($i) => $i->product->seller)->unique('id');
            foreach ($sellers as $seller) {
                $seller->notify(new \App\Notifications\SystemNotification(
                    'Pesanan Baru!',
                    "Seseorang telah memesan produk Anda. Silakan cek detail pesanan.",
                    'order'
                ));
            }

            return $order->load('items.product');
        });
    }
}
