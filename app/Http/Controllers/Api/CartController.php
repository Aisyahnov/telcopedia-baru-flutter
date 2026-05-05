<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Models\Voucher;
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

        try {
            $cart = $this->cartService->addToCart($request->user()->id, $request->product_id, $request->quantity);
            return response()->json(['data' => $cart, 'message' => 'Item added to cart']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
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

        try {
            $cart = $this->cartService->updateCartQuantity($request->user()->id, $request->item_id, $request->quantity);
            return response()->json(['data' => $cart, 'message' => 'Cart updated']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function applyVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $voucher = Voucher::where('code', $request->code)
            ->where('valid_until', '>=', now())
            ->first();

        if (!$voucher) {
            return response()->json(['message' => 'Voucher tidak valid atau sudah kadaluarsa'], 422);
        }

        return response()->json([
            'success' => true,
            'discount_amount' => (float)$voucher->discount_amount,
            'message' => 'Voucher berhasil digunakan: Potongan ' . number_format($voucher->discount_amount, 0, ',', '.')
        ]);
    }
}
