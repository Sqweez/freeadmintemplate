<?php


namespace App\Http\Controllers\Services;


use App\v2\Models\ProductSku;

class CartService {

    public function all() {
        return (ProductSku::with(ProductSku::PRODUCT_SKU_WITH_CART_LIST)
            ->orderBy('product_id')
            ->orderBy('id')
            ->get());
    }
}
