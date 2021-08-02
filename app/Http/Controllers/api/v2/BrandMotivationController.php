<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Product;
use App\SaleProduct;
use App\Store;
use App\v2\Models\BrandMotivation;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;

class BrandMotivationController extends Controller
{
    public function store(Request $request) {
        BrandMotivation::truncate();
        $motivations = $request->get('motivations');
        foreach ($motivations as $motivation) {
            BrandMotivation::create($motivation);
        }
        return BrandMotivation::all();
    }

    public function index() {
        return BrandMotivation::all();
    }
}
