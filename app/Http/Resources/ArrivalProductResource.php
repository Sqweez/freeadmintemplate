<?php

namespace App\Http\Resources;
use App\ArrivalProducts;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Arrival
 *
 * @mixin ArrivalProducts
 * */

class ArrivalProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $image = ($this->product->general_images->map(function($image) {
                return collect($image)->only('image');
            })->first()['image'])  ?? null;

        return [
            'attributes' => collect($this->product->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })->merge(collect($this->product->product->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })),
            'id' => $this->product->id,
            'count' => $this->count,
            'available_booking_count' => $this->available_booking_count,
            'booking_count' => $this->booking_count,
            'product_name' => $this->product->product_name,
            'product_price' => $this->product->product_price,
            'purchase_price' => $this->purchase_price,
            'manufacturer' => $this->product->manufacturer,
            'arrival_product_id' => $this->id,
            'bookingProducts' => $this->bookingProducts,
            'is_new' => $this->is_new_product,
            'product_image' => $image ? (url('/') . \Storage::url($image)) : 'https://iron-addicts.kz/images/logo_new.png',
        ];
    }
}
