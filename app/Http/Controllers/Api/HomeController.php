<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\FavoriteService;
use App\Models\Category;
use App\Models\Voucher;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $productService;
    protected $favoriteService;

    public function __construct(ProductService $productService, FavoriteService $favoriteService)
    {
        $this->productService = $productService;
        $this->favoriteService = $favoriteService;
    }

    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $categoryId = $request->query('category_id');

        $products = $this->productService->searchProducts($keyword, $categoryId);
        $recommendedProducts = $this->productService->getRecommendedProducts(auth('sanctum')->id());
        
        return response()->json([
            'data' => $products,
            'recommended_products' => $recommendedProducts
        ]);
    }

    public function showProduct(Request $request, $id)
    {
        $product = \App\Models\Product::with(['seller', 'category', 'reviews.user', 'images'])->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        \App\Models\ProductView::create([
            'user_id' => auth('sanctum')->id(),
            'product_id' => $product->id,
        ]);

        $relatedProducts = \App\Models\Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('status', 'approved')
            ->latest()
            ->take(8)
            ->get();

        $isFavorited = false;
        if (auth('sanctum')->check()) {
            $isFavorited = $this->favoriteService->isFavorited(auth('sanctum')->id(), $id);
        }

        return response()->json([
            'data' => [
                'product' => $product,
                'related_products' => $relatedProducts,
                'is_favorited' => $isFavorited
            ]
        ]);
    }

    public function categories()
    {
        $categories = Category::whereNull('parent_id')->get();
        return response()->json(['data' => $categories]);
    }

    public function favorites(Request $request)
    {
        $favorites = $this->favoriteService->getUserFavorites($request->user()->id, true, 12);
        return response()->json($favorites);
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $added = $this->favoriteService->toggleFavorite($request->user()->id, $request->product_id);
        
        return response()->json([
            'message' => $added ? 'Added to favorites' : 'Removed from favorites'
        ]);
    }

    public function vouchers()
    {
        $vouchers = Voucher::where(function($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            })
            ->latest()
            ->paginate(10);
        return response()->json($vouchers);
    }

    public function sellerProfile($id)
    {
        $seller = \App\Models\User::where('role', 'seller')->find($id);
        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        $products = \App\Models\Product::where('seller_id', $id)
            ->where('status', 'approved')
            ->with('category')
            ->latest()
            ->paginate(12);
            
        $reviews = \App\Models\Review::where('seller_id', $id)
            ->with('user', 'product')
            ->whereNotNull('seller_rating')
            ->latest()
            ->paginate(10);
            
        $avgSellerRating = \App\Models\Review::where('seller_id', $id)->avg('seller_rating') ?? 0;
        
        return response()->json([
            'seller' => $seller,
            'products' => $products,
            'reviews' => $reviews,
            'rating' => (float)$avgSellerRating
        ]);
    }
}
