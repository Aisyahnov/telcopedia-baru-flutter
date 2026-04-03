<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $items = $this->cartService->getUserCart($request->user()->id);
        $cartDetails = $this->cartService->getCartTotal($request->user()->id);
        
        return view('cart.index', [
            'items' => $items,
            'subtotal' => $cartDetails['subtotal'],
            'admin_fee' => $cartDetails['admin_fee'],
            'discount' => $cartDetails['discount'],
            'total' => $cartDetails['total']
        ]);
    }

    public function applyVoucher(Request $request)
    {
        $request->validate(['code' => 'required']);
        
        try {
            $this->cartService->applyVoucher($request->user()->id, $request->code);
            return back()->with('success', 'Voucher berhasil dipasang!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeVoucher(Request $request)
    {
        $this->cartService->removeVoucher($request->user()->id);
        return back()->with('success', 'Voucher dilepas.');
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $this->cartService->addToCart($request->user()->id, $request->product_id, $request->quantity ?? 1);
        return redirect()->route('cart.index')->with('success', 'Berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request)
    {
        $request->validate(['itemId' => 'required', 'quantity' => 'required|integer|min:1']);
        
        $item = \App\Models\CartItem::where('id', $request->itemId)->whereHas('cart', function($q) use($request) {
            $q->where('user_id', $request->user()->id);
        })->first();
        if ($item) { $item->update(['quantity' => $request->quantity]); }
        
        return back()->with('success', 'Keranjang diperbarui');
    }

    public function remove(Request $request, $itemId)
    {
        $this->cartService->removeCartItem($request->user()->id, $itemId);
        return back()->with('success', 'Item dihapus dari keranjang');
    }
}
