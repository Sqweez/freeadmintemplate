<?php

namespace App\Actions\Transfers;

use App\Exceptions\ClientException;
use App\Repository\ProductBatchRepository;
use App\Store;
use App\v2\Models\Matrix;
use Exception;

class RetrieveMatrixBasedTransferAction
{

    private ProductBatchRepository $productBatchRepository;

    public function __construct(ProductBatchRepository $productBatchRepository)
    {
        $this->productBatchRepository = $productBatchRepository;
    }

    /**
     * @throws Exception
     */
    public function handle(Store $store, Store $parentStore): \Illuminate\Support\Collection
    {
        $matrix = Matrix::query()
            ->where('store_id', $store->id)
            ->first();
        if (!$matrix) {
            throw new ClientException('Товарная матрица для этого склада не создана!');
        }

        $matrixProducts = $matrix->products;
        $productIds = array_column($matrixProducts, 'id');
        $productBatches = $this->productBatchRepository->getProductTotalQuantities($productIds, $store);
        $needleProducts = collect($matrixProducts)
            ->filter(function ($product) use ($productBatches) {
                return $productBatches
                    ->where('product_id', $product['id'])
                    ->where('total_quantity', '>=', $product['count'])
                    ->isEmpty();
            })
            ->map(function ($product) use ($productBatches) {
                $item = $productBatches
                    ->where('product_id', $product['id'])
                    ->first();
                return [
                    'id' => $product['id'],
                    'count' => $product['count'] - ($item['total_quantity'] ?? 0),
                ];
            })
            ->values();

        $existingProductBatches = $this->productBatchRepository->getProductTotalQuantities($needleProducts->pluck('id')->toArray(), $parentStore);
        return $existingProductBatches
            ->map(function ($product) use ($needleProducts) {
                $item = $needleProducts
                    ->where('product_id', $product['id'])
                    ->first();
                return [
                    'id' => $product['product_id'],
                    'count' => min($product['total_quantity'], $item['count'])
                ];
            });
    }
}
