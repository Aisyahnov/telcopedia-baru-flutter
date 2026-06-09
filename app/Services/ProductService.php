<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function searchProducts($keyword = null, $categoryId = null)
    {
        $bannedSellerIds = \App\Models\User::where('role', 'seller')->get()
            ->filter(function($user) { return $user->is_banned_from_posting; })
            ->pluck('id');

        $query = Product::query()->where('status', 'approved')
            ->whereNotIn('seller_id', $bannedSellerIds)
            ->with('category', 'seller');

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                // Exact word match to prevent "meja" from matching "kemeja"
                $q->where('name', 'like', $keyword . ' %')
                  ->orWhere('name', 'like', '% ' . $keyword . ' %')
                  ->orWhere('name', 'like', '% ' . $keyword)
                  ->orWhere('name', $keyword)
                  
                  ->orWhere('description', 'like', $keyword . ' %')
                  ->orWhere('description', 'like', '% ' . $keyword . ' %')
                  ->orWhere('description', 'like', '% ' . $keyword)
                  ->orWhere('description', $keyword)
                  
                  ->orWhereHas('seller', function($sq) use ($keyword) {
                      $sq->where('name', 'like', $keyword . ' %')
                         ->orWhere('name', 'like', '% ' . $keyword . ' %')
                         ->orWhere('name', 'like', '% ' . $keyword)
                         ->orWhere('name', $keyword);
                  });
            });
        }

        if ($categoryId) {
            $categoryIds = \App\Models\Category::where('id', $categoryId)
                ->orWhere('parent_id', $categoryId)
                ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }

        return $query->latest()->paginate(8);
    }

    public function getSellerProducts($sellerId)
    {
        return Product::where('seller_id', $sellerId)->with('category')->latest()->get();
    }

    public function createProduct($sellerId, array $data)
    {
        $data['seller_id'] = $sellerId;
        return Product::create($data);
    }

    public function updateProduct($productId, $sellerId, array $data)
    {
        $product = Product::where('id', $productId)->where('seller_id', $sellerId)->firstOrFail();
        $product->update($data);
        return $product;
    }

    public function deleteProduct($productId, $sellerId)
    {
        $product = Product::where('id', $productId)->where('seller_id', $sellerId)->firstOrFail();
        return $product->delete();
    }

    public function getRecommendedProducts($userId = null, $limit = 8)
    {
        $bannedSellerIds = \App\Models\User::where('role', 'seller')->get()
            ->filter(function($user) { return $user->is_banned_from_posting ?? false; })
            ->pluck('id');

        $baseQuery = Product::where('status', 'approved')
            ->whereNotIn('seller_id', $bannedSellerIds)
            ->with('category', 'seller', 'images');

        if ($userId) {
            $topCategories = \App\Models\ProductView::where('user_id', $userId)
                ->join('products', 'product_views.product_id', '=', 'products.id')
                ->select('products.category_id', \Illuminate\Support\Facades\DB::raw('count(*) as view_count'))
                ->groupBy('products.category_id')
                ->orderByDesc('view_count')
                ->limit(3)
                ->pluck('category_id');

            if ($topCategories->isNotEmpty()) {
                $recommended = (clone $baseQuery)->whereIn('category_id', $topCategories)
                    ->inRandomOrder()
                    ->take($limit)
                    ->get();

                if ($recommended->count() < $limit) {
                    $fallback = (clone $baseQuery)->whereNotIn('id', $recommended->pluck('id'))
                        ->inRandomOrder()
                        ->take($limit - $recommended->count())
                        ->get();
                    $recommended = $recommended->concat($fallback);
                }
                return $recommended;
            }
        }

        $topGlobalProductIds = \App\Models\ProductView::select('product_id', \Illuminate\Support\Facades\DB::raw('count(*) as view_count'))
            ->groupBy('product_id')
            ->orderByDesc('view_count')
            ->limit($limit * 2)
            ->pluck('product_id');

        if ($topGlobalProductIds->isNotEmpty()) {
            return (clone $baseQuery)->whereIn('id', $topGlobalProductIds)
                ->inRandomOrder()
                ->take($limit)
                ->get();
        }

        return (clone $baseQuery)->inRandomOrder()->take($limit)->get();
    }
}
