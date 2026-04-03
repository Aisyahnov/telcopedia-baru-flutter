<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function searchProducts($keyword = null, $categoryId = null)
    {
        $query = Product::query()->with('category', 'seller');

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->latest()->paginate(12);
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
}
