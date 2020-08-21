<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $products = BatchResource::collection($this->batches);
        $products = $products->toArray($request);

        return [
            'id' => $this->id,
            'parent_store' => $this->parent_store_id,
            'child_store' => $this->child_store_id,
            'products' => $this->groupProducts($products)
        ];
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
                'product_name' => $product[0]['product_name'],
                'attributes' => $product[0]['attributes'],
                'product_id' => $product[0]['product_id'],
                'batch_id' => $product[0]['batch_id'],
                'manufacturer' => $product[0]['manufacturer'],
                'product_price' => $product[0]['product_price']
            ]);
        }

        return $result;
    }

}
