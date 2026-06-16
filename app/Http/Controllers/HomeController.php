<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\FavoriteService;
use App\Models\Product;

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
        $products = $this->productService->searchProducts($request->query('keyword'), $request->query('category_id'));
        $recommendedProducts = $this->productService->getRecommendedProducts(auth()->id());
        return view('home', compact('products', 'recommendedProducts'));
    }

    public function showProduct($id)
    {
        $product = Product::where('status', 'approved')->with('seller', 'category', 'images')->findOrFail($id);
        
        \App\Models\ProductView::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        $relatedProducts = Product::with('seller')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('status', 'approved')
            ->latest()
            ->get()
            ->filter(function ($p) {
                return !optional($p->seller)->is_banned_from_posting;
            });

        if ($relatedProducts->count() < 4) {
            $fallbackProducts = Product::with('seller')
                ->where('id', '!=', $id)
                ->where('status', 'approved')
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->inRandomOrder()
                ->take(8 - $relatedProducts->count())
                ->get()
                ->filter(function ($p) {
                    return !optional($p->seller)->is_banned_from_posting;
                });
            
            $relatedProducts = $relatedProducts->merge($fallbackProducts);
        }

        $relatedProducts = $relatedProducts->take(8);
            
        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = $this->favoriteService->isFavorited(auth()->id(), $id);
        }
            
        return view('product.show', compact('product', 'relatedProducts', 'isFavorited'));
    }

    public function about()
    {
        return view('about');
    }

    public function help()
    {
        return view('help');
    }

    public function favorites(Request $request)
    {
        $keyword = $request->query('keyword');
        $favorites = $this->favoriteService->getUserFavorites($request->user()->id, true, 12, $keyword);
        return view('favorites.index', compact('favorites', 'keyword'));
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $this->favoriteService->toggleFavorite($request->user()->id, $request->product_id);
        return back()->with('success', 'Favorite diupdate');
    }

    public function sellerProfile($id)
    {
        $seller = \App\Models\User::where('role', 'seller')->findOrFail($id);
        $products = Product::where('seller_id', $id)->where('status', 'approved')->with('category')->latest()->paginate(12, ['*'], 'p_page');
        
        $reviews = \App\Models\Review::where('seller_id', $id)
            ->with('user')
            ->whereNotNull('seller_rating')
            ->latest()
            ->paginate(10, ['*'], 'r_page');
            
        $avgSellerRating = \App\Models\Review::where('seller_id', $id)->avg('seller_rating') ?? 0;
        
        return view('seller.profile', compact('seller', 'products', 'reviews', 'avgSellerRating'));
    }
}
