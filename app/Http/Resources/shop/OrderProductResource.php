<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\shop\ProductChildResource;


class OrderProductResource extends JsonResource
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
            'product_id' => intval($this->id),
            'product_price' => $this->product_price,
            'product_name' => $this->product_name,
            'product_image' => $this->getProductImages($this->product_images, 'image')[0],
            'product_weight' => $this->getProductWeight($this->attributes) ?? '',
            'product_taste' => $this->getProductTaste($this->attributes) ?? '-',
        ];
    }

    private function getProductImages($_images, $type) {
        if ($_images->count() === 0) {
            return [
                url('/') . Storage::url($type === 'image' ? 'products/product_image_default.jpg': 'product_thumbs/product_image_default.jpg')
            ];
        }
        return $_images->map(function ($i) use ($type) {
            return url('/') . Storage::url($i->product_image ?? ($type === 'image' ? 'products/product_image_default.jpg': 'product_thumbs/product_image_default.jpg'));
        });
    }

    private function getProductWeight($attributes) {
        return $attributes->filter(function ($i) {
                return $i['attribute_id'] == 2;
            })->first()['attribute_value'] ?? '';
    }

    private function getProductTaste($attributes) {
        return $attributes->filter(function ($i) {
                return $i['attribute_id'] == 1;
            })->first()['attribute_value'] ?? '';
    }
}
