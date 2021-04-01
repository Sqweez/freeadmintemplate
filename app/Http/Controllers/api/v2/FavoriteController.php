<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\shop\ProductsResource;
use App\v2\Models\Favorite;
use App\v2\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request) {
        $product_id = $request->get('product_id');
        $user_token = $request->get('user_token');
        $favorite = Favorite::whereUserToken($user_token)->whereProductId($product_id)->first();
        if ($favorite) {
            $favorite->delete();
        } else {
            Favorite::create([
                'product_id' => $product_id,
                'user_token' => $user_token
            ]);
        }
    }

    public function index(Request $request) {
        $user_token = $request->get('user_token');
        $favorites = Favorite::whereUserToken($user_token)->select('product_id')->get()->pluck('product_id');
        $productQuery = Product::query()->whereIn('id', $favorites)->whereIsSiteVisible(true);
        $productQuery->whereHas('category', function ($q) {
            return $q->where('is_site_visible', true);
        });
        $productQuery->whereHas('subcategory', function ($q) {
            return $q->where('is_site_visible', true);
        });
        $productQuery->with(['subcategory', 'attributes', 'product_thumbs']);
        $productQuery->with(['favorite' => function ($query) use ($user_token) {
            return $query->where('user_token', $user_token);
        }]);
        $productQuery->orderBy('product_name');

        return ProductsResource::collection($productQuery->get());
    }
}
