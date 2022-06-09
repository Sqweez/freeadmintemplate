<?php

namespace App\Http\Resources\shop;

use App\Http\Controllers\api\v2\CommentController;
use App\Http\Resources\ProductCommentResource;
use App\Http\Resources\shop\v2\ProductSkuResource;
use App\v2\Models\Product;
use App\v2\Models\RelatedProduct;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductResource
 * @package App\Http\Resources\shop
 * @mixin Product
 */

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
            'product_id' => $this->id,
            'product_price' => $this->product_price,
            'subcategory' => $this->subcategory->subcategory_name,
            'product_name' => strlen($this->product_name_web) ? $this->product_name_web : $this->product_name,
            'product_description' => $this->product_description,
            'attributes' => $this->attributes->pluck('attribute_value'),
            'product_images' => $this->product_images->count() > 0 ? $this->product_images->pluck('image')->map(function ($image) {
                return url('/') . Storage::url($image);
            })->first() : url('/') . Storage::url('products/product_image_default.jpg'),
            'is_hit' => $this->is_hit,
            'is_site_visible' => $this->is_site_visible,
            'skus' => collect(ProductSkuResource::collection($this->sku))->toArray(),
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'has_group' => intval($this->grouping_attribute_id) > 0,
            'meta_title' => $this->meta_title ?? '',
            'meta_description' => $this->meta_description ?? '',
            'stock_price' => $this->stock_price,
            'has_stock' => $this->stock_price !== $this->product_price,
            'related_products' => ProductsResource::collection(Product::whereIn('id',
                RelatedProduct::whereCategoryId($this->category_id)->get()->pluck('product_id')
            )->get()),
            'comments' => CommentController::parseComments($this->comments),
            'manufacturer' => $this->manufacturer
        ];
    }
}
