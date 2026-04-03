<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        return response()->json([
            'data' => $this->cartService->getUserCart($request->user()->id),
            'total' => $this->cartService->getCartTotal($request->user()->id)
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->cartService->addToCart($request->user()->id, $request->product_id, $request->quantity);
        return response()->json(['data' => $cart, 'message' => 'Item added to cart']);
    }

    public function remove(Request $request, $itemId)
    {
        $this->cartService->removeCartItem($request->user()->id, $itemId);
        return response()->json(['message' => 'Item removed from cart']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);
        // Update logic implementation would go here inside service
        return response()->json(['message' => 'Cart updated']);
    }

    public function applyVoucher(Request $request)
    {
        // Pengecekan validasi voucher oleh request
        return response()->json(['message' => 'Voucher applied successfully']);
    }
}
