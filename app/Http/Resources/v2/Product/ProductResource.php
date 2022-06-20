<?php

namespace App\Http\Resources\v2\Product;

use App\Http\Controllers\api\v2\CommentController;
use App\Http\Resources\AttributeResource;
use App\Product;
use App\v2\Models\ProductSku;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class User
 *
 * @mixin ProductSku
 * */

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'product_price' => $this->product_price,
            'self_price' => $this->self_price,
            'product_barcode' => $this->product_barcode,
            'is_hit' => $this->is_hit,
            'is_site_visible' => $this->is_site_visible,
            'category' => $this->category->id,
            'subcategory' => $this->subcategory->id,
            'additional_subcategories' => $this->additionalSubcategories->pluck('id'),
            'manufacturer' => $this->manufacturer->id,
            'attributes' => collect($this->attributes)->map(function ($i) {
                return [
                    'attribute_id' => $i['attribute_id'],
                    'attribute_value' => $i['attribute_value']
                ];
            })->merge(collect($this->product->attributes)->map(function ($i) {
                return [
                    'attribute_id' => $i['attribute_id'],
                    'attribute_value' => $i['attribute_value']
                ];
            })),
            'tags' => $this->tags,
            'prices' => $this->prices,
            'product_images' => $this->general_images->map(function($image) {
                return collect($image)->only('image');
            }),
            'product_thumbs' => $this->general_thumbs->map(function ($image) {
                return collect($image)->only('image');
            }),
            'sku_count' => $this->relative_sku_count,
            'grouping_attribute_id' => $this->grouping_attribute_id,
            'product_id' => $this->product_id,
            'product_sku_images' => $this->product_images,
            'product_sku_thumbs' => $this->product_thumbs,
            'kaspi_product_price' => $this->kaspi_product_price,
            'is_kaspi_visible' => $this->is_kaspi_visible,
            'is_iherb' => $this->is_iherb,
            'supplier' => $this->product->supplier,
            'supplier_id' => $this->product->supplier_id,
            'meta_title' => $this->product->meta_title,
            'meta_description' => $this->product->meta_description ?? '',
            'product_name_web' => $this->product->product_name_web,
            'comments' => CommentController::parseComments($this->product->comments)
        ];
    }
}
