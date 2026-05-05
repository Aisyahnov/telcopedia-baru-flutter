<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Voucher;

class CartService
{
    public function getUserCart($userId)
    {
        return Cart::with(['items.product', 'voucher'])->firstOrCreate(['user_id' => $userId]);
    }

    public function applyVoucher($userId, $code)
    {
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            throw new \Exception("Kode voucher tidak ditemukan.");
        }

        if ($voucher->valid_until && $voucher->valid_until < now()->toDateString()) {
            throw new \Exception("Voucher sudah kadaluarsa.");
        }

        $cart = $this->getUserCart($userId);
        
        // Hitung subtotal saat ini
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += ($item->quantity * $item->product->price);
        }

        if ($voucher->min_spend > $subtotal) {
            throw new \Exception("Belanja minimal Rp " . number_format($voucher->min_spend, 0, ',', '.') . " untuk menggunakan voucher ini.");
        }

        $cart->update(['voucher_id' => $voucher->id]);

        return $cart;
    }

    public function removeVoucher($userId)
    {
        $cart = $this->getUserCart($userId);
        $cart->update(['voucher_id' => null]);
        return $cart;
    }

    public function addToCart($userId, $productId, $quantity, $overrideQuantity = false)
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $product = Product::findOrFail($productId);
        
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $productId)
                            ->first();

        $targetQuantity = $quantity;
        if ($cartItem && !$overrideQuantity) {
            $targetQuantity += $cartItem->quantity;
        }

        if ($targetQuantity > $product->stock) {
            throw new \Exception("Stok tidak mencukupi. Stok tersedia: " . $product->stock);
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $targetQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $targetQuantity,
            ]);
        }

        return $this->getUserCart($userId);
    }

    public function updateCartQuantity($userId, $itemId, $quantity)
    {
        $cart = $this->getUserCart($userId);
        $cartItem = $cart->items()->where('id', $itemId)->first();

        if (!$cartItem) {
            throw new \Exception("Item tidak ditemukan di keranjang.");
        }

        if ($quantity > $cartItem->product->stock) {
            throw new \Exception("Stok tidak mencukupi. Stok tersedia: " . $cartItem->product->stock);
        }

        $cartItem->update(['quantity' => $quantity]);
        return $this->getUserCart($userId);
    }

    public function removeCartItem($userId, $cartItemId)
    {
        $cart = Cart::where('user_id', $userId)->first();
        if ($cart) {
            CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->delete();
        }
        return true;
    }

    public function getCartTotal($userId)
    {
        $cart = $this->getUserCart($userId);
        $subtotal = 0;
        
        foreach ($cart->items as $item) {
            $subtotal += ($item->quantity * $item->product->price);
        }

        $adminFee = $subtotal * 0.05; // 5% Admin Fee
        $discount = $cart->voucher ? $cart->voucher->discount_amount : 0;
        
        $total = $subtotal + $adminFee - $discount;
        
        return [
            'subtotal' => $subtotal,
            'admin_fee' => $adminFee,
            'discount' => $discount,
            'total' => max(0, $total)
        ];
    }
}
