<?php

namespace App\Http\Resources\v3\Product;

use App\MarginType;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductSku
 */
class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $product =  [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'category' => $this->category,
            'subcategory_id' => $this->product->subcategory_id,
            'manufacturer' => $this->manufacturer,
            'manufacturer_id' => $this->manufacturer->id,
            'attributes' => $this->retrieveAttributes(),
            'product_barcode' => $this->product_barcode,
            'product_price' => $this->product_price,
            'base_price' => $this->product_price,
            'product_price_rub' => $this->product->product_price_rub,
            'quantity' => $this->getQuantity($request),
            'sku_can_be_created' => !!$this->grouping_attribute_id,
            'grouping_attribute_id' => $this->grouping_attribute_id,
            'product_id' => $this->product_id,
            'prices' => $this->prices,
            'product_name_web' => $this->product_name_web,
            'margin_type' => $this->margin_type ? $this->margin_type->only(['id', 'title']) : MarginType::find(
                $this->margin_type_id
            ),
            'is_kaspi_visible' => $this->is_kaspi_visible,
            'is_iherb' => $this->is_iherb,
            'iherb_price' => $this->iherb_price,
            'is_dubai' => $this->product->is_dubai,
            'is_opt' => $this->product->is_opt,
            'positive_batches' => $this->relationLoaded('positive_batches') ? $this->positive_batches : null
        ];

        if ( $this->isNestedRelationLoaded($this, ['product', 'wholesale_prices'])) {
            $product['wholesale_prices'] = $this->product->wholesale_prices;
        }

        return $product;
    }

    private function getQuantity(Request $request)
    {
        if (!$this->relationLoaded('positive_batches') || !$request->has('store_id')) {
            return 0;
        }
        return $this->positive_batches->where('store_id', $request->get('store_id'))->sum('quantity');
    }

    protected function isNestedRelationLoaded($model, array $relations): bool
    {
        foreach ($relations as $relation) {
            if (!$model->relationLoaded($relation)) {
                return false;
            }

            $model = $model->getRelation($relation);
        }

        return true;
    }

    private function retrieveAttributes()
    {
        if ($this->relationLoaded('attributes')) {
            $skuAttributes = collect($this->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                    'is_main' => false
                ];
            });
        } else {
            $skuAttributes = collect([]);
        }
        $productAttributes = collect($this->product->attributes)->map(function ($attribute) {
            return [
                'attribute_value' => $attribute->attribute_value,
                'attribute_name' => $attribute->attribute_name->attribute_name,
                'is_main' => true
            ];
        });

        return $skuAttributes->merge($productAttributes);
    }
}
