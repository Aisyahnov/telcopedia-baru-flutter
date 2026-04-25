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
        return view('home', compact('products'));
    }

    public function showProduct($id)
    {
        $product = Product::where('status', 'approved')->with('seller', 'category', 'images')->findOrFail($id);
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('status', 'approved')
            ->latest()
            ->take(8)
            ->get();
            
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

    public function contact()
    {
        return view('contact');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }

    public function favorites(Request $request)
    {
        $favorites = $this->favoriteService->getUserFavorites($request->user()->id);
        return view('favorites.index', compact('favorites'));
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
        $products = Product::where('seller_id', $id)->where('status', 'approved')->with('category')->latest()->paginate(12);
        
        return view('seller.profile', compact('seller', 'products'));
    }
}
