<?php

namespace App\Services;

use App\Models\Favorite;

class FavoriteService
{
    public function getUserFavorites($userId, $paginate = false, $perPage = 10, $keyword = null)
    {
        $query = Favorite::where('user_id', $userId)->with(['product.seller', 'product.category']);

        if ($keyword) {
            $query->whereHas('product', function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword . ' %')
                  ->orWhere('name', 'like', '% ' . $keyword . ' %')
                  ->orWhere('name', 'like', '% ' . $keyword)
                  ->orWhere('name', $keyword)
                  ->orWhereHas('seller', function ($sq) use ($keyword) {
                      $sq->where('name', 'like', $keyword . ' %')
                         ->orWhere('name', 'like', '% ' . $keyword . ' %')
                         ->orWhere('name', 'like', '% ' . $keyword)
                         ->orWhere('name', $keyword);
                  });
            });
        }

        if ($paginate) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function toggleFavorite($userId, $productId)
    {
        $favorite = Favorite::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($favorite) {
            $favorite->delete();
            return false; // Removed
        }

        Favorite::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return true; // Added
    }

    public function isFavorited($userId, $productId)
    {
        return Favorite::where('user_id', $userId)->where('product_id', $productId)->exists();
    }
}
