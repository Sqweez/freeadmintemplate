<?php

namespace App\Resolvers\ProductSku;

use App\Concerns\UseQuickBindings;
use App\v2\Models\ProductSku;

class ProductSkuNameResolver {

    use UseQuickBindings;

    public function resolve(ProductSku $productSku): string {
        $name = $productSku->product->product_name;
        $attributesSku = $productSku->attributes->pluck('attribute_value')->join(' ');
        $attributeProduct = $productSku->product->attributes->pluck('attribute_value')->join(' ');
        return sprintf("%s, %s, %s", $name, $attributeProduct, $attributesSku);
    }
}
