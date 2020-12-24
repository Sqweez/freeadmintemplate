<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Resources\v2\Product\ProductsResource;
use App\Http\Resources\v2\Product\ProductResource;
use App\v2\Models\Product;
use App\ProductBatch;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ProductService;

class ProductController extends Controller
{
    private function prepareSearchString($search) {
        return "%" . str_replace(' ', '%', $search) . "%";
    }

    /*
     * Получение всех товаров
     * */

    public function index() {
        return ProductsResource::collection(ProductService::all());
    }

    /*
    * Получение одного товара
    */
    public function show($id) {
        return new ProductResource(ProductService::get($id));
    }

    public function store(ProductCreateRequest $request) {
        $product = ProductService::getProductFields($request);
        $product_attributes = ProductService::getRelationFields($request);
        $product_sku_attributes = ProductService::getSkuFields($request);
        $product = ProductService::create($product, $product_attributes);
        return ProductService::createSku($product, $product_sku_attributes);
    }

    public function createProductSku(Product $product, Request $request) {
        $product_sku_attributes = ProductService::getSkuFields($request);
        return ProductService::createSku($product, $product_sku_attributes);
    }

    public function updateProductSku(ProductSku $sku, Request $request) {
        $product_sku_attributes = ProductService::getSkuFields($request);
        return ProductService::updateSku($sku, $product_sku_attributes);
    }

    public function getProductsQuantity($store) {
        return ProductBatch::quantitiesOfStore($store)->get();
    }

    public function update(Product $product, Request $request) {
        $product_attributes = ProductService::getProductFields($request);
        $product_fields = ProductService::getRelationFields($request);
        ProductService::updateProduct($product, $product_attributes, $product_fields);
        $product->fresh();
        return ProductsResource::collection(ProductSku::whereProductId($product->id)->with(ProductSku::PRODUCT_SKU_WITH_ADMIN_LIST)->get());
    }

    public function delete($id) {
        ProductService::delete($id);
    }

    public function groupProducts() {
        $products = Product::select(['id', 'product_price', 'product_name', 'manufacturer_id'])->get();
        $products = $products->groupBy(['product_name', 'product_price', 'manufacturer_id']);

        $products->each(function ($price) {
            collect($price)->each(function ($manufacturer) {
                collect($manufacturer)->each(function ($product) {
                    $ids = collect($product)->pluck('id');
                    $group_id = $ids->first();
                    Product::where('id', $ids)->update([
                        'group_id' => $group_id
                    ]);
                });
            });
        });
    }

    public function addProductQuantity($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|numeric',
            'quantity' => 'required|numeric',
            'purchase_price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Переданы некорректные данные'
            ], 422);
        }

        $store_id = $request->get('store_id');
        $quantity = $request->get('quantity');
        $purchase_price = $request->get('purchase_price');

        ProductService::addQuantity($id, $store_id, $quantity, $purchase_price);
        return ProductService::getQuantityByProduct($id, $store_id);
    }

    public function changeCount($id, Request $request) {
        $batchQuery = ProductBatch::query()->where('store_id', $request->get('store_id'))->where('product_id', $id);
        if (intval($request->get('increment')) === -1) {
            $batchQuery->where('quantity', '>', 0);
        }

        $batch = $batchQuery->orderBy('created_at', 'desc')->first();
        if (!$batch) {
            return response()->json(['message' => 'По данному товару не было поставок!'], 500);
        }

        $batch->quantity += $request->get('increment');

        $batch->save();

        return $batch;
    }
}
