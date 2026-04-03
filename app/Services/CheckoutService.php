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

    public function processCheckout($userId, $shippingAddress)
    {
        return DB::transaction(function () use ($userId, $shippingAddress) {
            $cart = Cart::where('user_id', $userId)->with('items.product')->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                throw new \Exception("Keranjang belanja kosong.");
            }

            $cartDetails = $this->cartService->getCartTotal($userId);
            
            $order = Order::create([
                'user_id' => $userId,
                'shipping_address' => $shippingAddress,
                'voucher_id' => $cart->voucher_id,
                'subtotal_amount' => $cartDetails['subtotal'],
                'discount_amount' => $cartDetails['discount'],
                'admin_fee' => $cartDetails['admin_fee'],
                'total_amount' => $cartDetails['total'],
                'status' => 'pending_payment',
            ]);

            foreach ($cart->items as $item) {
                // Potong Stok
                $item->product->decrement('stock', $item->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Kosongkan Keranjang & Lepas Voucher
            $cart->update(['voucher_id' => null]);
            $cart->items()->delete();

            return $order->load('items.product');
        });
    }
}
