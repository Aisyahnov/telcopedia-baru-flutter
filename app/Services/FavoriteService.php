<?php

namespace App\Services;

use App\Models\Favorite;

class FavoriteService
{
    public function getUserFavorites($userId)
    {
        return Favorite::where('user_id', $userId)->with('product')->get();
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
}
