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

    public function processCheckout($userId, $shippingAddress, $paymentMethod = 'transfer', $productId = null, $cartItemIds = null)
    {
        return DB::transaction(function () use ($userId, $shippingAddress, $paymentMethod, $productId, $cartItemIds) {
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
                if ($cart->voucher) {
                    $discount = $this->cartService->calculateDiscount($subtotal, $cart->voucher);
                }
                $adminFee = $subtotal * 0.05;
                $total = $subtotal + $adminFee - $discount;
                $voucherId = $discount > 0 ? $cart->voucher_id : null;
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

            return $order->load('items.product');
        });
    }
}
