<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $KASPI_PERCENT = 11;
        $kaspi_red = $this->kaspi_red;
        $products = ProductSaleResource::collection($this->products);
        $purchase_price = array_reduce($products->toArray($products), function ($a, $c) {
            return $a + $c['purchase_price'];
        });
        $product_price = array_reduce($products->toArray($products), function ($a, $c) {
            return $a + $c['product_price'];
        });

        $_price = $this->getFinalPrice($this->discount, $product_price);

        $final_price = $_price - ($_price * $KASPI_PERCENT / 100) * $kaspi_red;

        $products = $products->toArray($products);

        return [
            'id' => $this->id,
            'discount' => $this->discount,
            'user' => $this->user->name,
            'user_id' => $this->user_id,
            'client' => $this->client->client_name,
            'store' => $this->store->name,
            'store_id' => intval($this->store_id),
            'products' => $this->groupProducts($products),
            'purchase_price' => $purchase_price,
            'fact_price' => $product_price,
            'final_price' => $final_price - intval($this->balance),
            'margin' => $this->discount != 100 ? $final_price - $purchase_price : 0,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'balance' => $this->balance,
        ];
    }

    private function getFinalPrice($discount, $price) {
        return intval($price - $price * $discount / 100);
    }

    private function groupProducts($products) {
        $_products = [];
        foreach ( $products as $value ) {
            $_products[$value['product_id']][] = $value;
        }
        $result = [];

        foreach ($_products as $product) {
            array_push($result, [
                'count' => count($product),
                'product_name' => $product[0]['product_name'] . " | " . $this->getAttributeString($product[0]['attributes']),
                'attributes' => $product[0]['attributes'],
                'manufacturer' => $product[0]['manufacturer'],
                'product_id' => $product[0]['product_id']
            ]);
        }

        return $result;
    }

    private function getAttributeString($attrs) {
        return array_reduce($attrs->toArray($attrs), function ($a, $c) {
            return $a . $c['attribute_value'] . " | ";
        });
    }
}
