<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Matrix\MatrixListResource;
use App\v2\Models\Matrix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MatrixController extends BaseApiController
{
    public function store(Request $request): JsonResponse {
        $store_id = $request->get('store_id');
        $products = $request->get('products', []);
        if (Matrix::query()->whereStoreId($store_id)->exists()) {
            return $this->respondError('Товарная матрица для данного склада уже существует');
        }
        Matrix::create([
            'store_id' => $store_id,
            'products' => $products
        ]);
        return $this->respondSuccess();
    }

    public function index(): AnonymousResourceCollection {
        return MatrixListResource::collection(
            Matrix::query()
                ->with(['store:id,name'])
                ->get()
        );
    }

    public function show(Matrix $matrix): MatrixListResource {
        return MatrixListResource::make($matrix);
    }

    public function update(Matrix $matrix, Request $request): JsonResponse {
        $matrix->products = $request->get('products', []);
        $matrix->save();
        return $this->respondSuccess();
    }
}
