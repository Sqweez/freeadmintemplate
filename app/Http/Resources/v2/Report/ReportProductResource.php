<?php

namespace App\Http\Resources\v2\Report;

use App\Repository\ProductRepository;
use App\SaleProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin SaleProduct */
class ReportProductResource extends JsonResource
{

    private $productRepository;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @mixin SaleProduct
     * @return array
     */

    public function toArray($request): array
    {
        $this->productRepository = new ProductRepository();
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product->product->product_name,
            'attributes' => $this->productRepository->getFullAttributeValues($this->product),
            '_attributes' => $this->productRepository->getFullAttributes($this->product),
            'manufacturer' => $this->product->manufacturer,
            'product_price' => $this->product_price,
          /*  'count' => $this->count,*/
            'discount' => $this->discount
        ];
    }
}
