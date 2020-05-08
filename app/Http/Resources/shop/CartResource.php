<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'user_token' => $this->user_token,
            'products' => CartProductResource::collection($this->products)
        ];
    }

    private function removeWrapping($array) {
        $output = [];
        foreach ($array as $item) {
            $output[] = $item[0];
        }
        return $output;
    }

    /*private function convertFilters($filters) {
        $array = [];

        foreach ($filters as $key => $filter) {
            foreach ($filter as $item) {
                $array[$key][] = $item;
            }
        }

        return $array;
    }*/

}
