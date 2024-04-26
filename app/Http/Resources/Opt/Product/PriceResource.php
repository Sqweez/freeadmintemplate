<?php

namespace App\Http\Resources\Opt\Product;

use App\v2\Models\WholesalePrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin WholesalePrice */
class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */

    protected $discount = 0;

    public function __construct($resource, $discount = 0)
    {
        parent::__construct($resource);
        $this->discount = $discount;
    }

    public function toArray($request): array
    {
        $originalPrice = $this->getOriginalPrice();
        $actualPrice = $this->getActualPrice($originalPrice);
        return [
            'currencySign' => $this->currency->unicode_symbol,
            'original_price' => $originalPrice,
            'price' => $actualPrice,
            'has_stock' => $actualPrice !== $originalPrice,
            'original_price_formatted' => price_format($originalPrice, $this->currency->unicode_symbol),
            'price_formatted' => price_format($actualPrice, $this->currency->unicode_symbol),
            'currency_id' => $this->currency_id,
            'discount' => $this->discount,
            //'isPriceSet' => !empty($prices)
        ];
    }

    public function getOriginalPrice(): int
    {
        return $this->price;
    }

    public function getActualPrice($originalPrice)
    {
        return $originalPrice * (1 - $this->discount / 100);
    }
}
