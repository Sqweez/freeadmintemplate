<?php

namespace App\Http\Resources\shop\v2;

use App\Http\Resources\shop\ProductsResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $products = collect([]);
        $this->productNews->each(function ($item) use (&$products) {
            collect($item->products)->each(function ($product) use (&$products){
                $products->push($product);
            });
        });

        return [
            'id' => $this->id,
            'title' => $this->title,
            'text' => $this->text,
            'image' => url('/') . \Storage::url($this->news_image[0]['image']),
            'short_text' => $this->short_text,
            'slug' => Str::slug($this->title) . '_' . Carbon::parse($this->created_at)->format('d.m.Y'),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
            'products' => ProductsResource::collection($products),
        ];
    }
}
