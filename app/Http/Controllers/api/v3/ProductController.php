<?php

namespace App\Http\Controllers\api\v3;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\v3\Product\ProductsResource;
use App\Repository\v2\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function getProducts(Request $request, ProductRepository $productRepository)
    {
        return ProductsResource::collection(
            $productRepository->retrieveProducts($request->all())
        );
    }
}
