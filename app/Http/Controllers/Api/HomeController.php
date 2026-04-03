<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\FavoriteService;
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
        return response()->json(['data' => $products]);
    }

    public function favorites(Request $request)
    {
        $favorites = $this->favoriteService->getUserFavorites($request->user()->id);
        return response()->json(['data' => $favorites]);
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $added = $this->favoriteService->toggleFavorite($request->user()->id, $request->product_id);
        
        return response()->json([
            'message' => $added ? 'Added to favorites' : 'Removed from favorites'
        ]);
    }
}
