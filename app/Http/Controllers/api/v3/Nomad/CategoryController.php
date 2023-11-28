<?php

namespace App\Http\Controllers\api\v3\Nomad;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Cache::remember('nomad_categories', 60 * 60 * 60, function () {
            $NOMAD_BRAND_ID = __hardcoded(608);
            return Category::query()
                ->whereHas('products', function ($query) use ($NOMAD_BRAND_ID) {
                    return $query->where('manufacturer_id', $NOMAD_BRAND_ID);
                })
                ->with('seoText')
                ->with(['subcategories' => function ($query) use ($NOMAD_BRAND_ID) {
                    return $query->whereHas('products', function ($subQuery) use ($NOMAD_BRAND_ID) {
                        return $subQuery->where('manufacturer_id', $NOMAD_BRAND_ID);
                    });
                }])
                ->get();
        });
        return CategoryResource::collection($categories);
    }
}
