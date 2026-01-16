<?php

namespace App\Services;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{

    public function index()
    {
    return Favorite::with('product')->where('user_id', Auth::id())->get();
    }
    public function toggleFavorite($productId)
    {
        $userId = Auth::id();

        $favorite = Favorite::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        if ($favorite) {
            $favorite->delete();
            return ['message' => __('apis.removed'), 'favorite' => false];
        } else {
            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            return ['message' => __('apis.added'), 'favorite' => true];
        }
    }
}
