<?php

namespace App\Http\Resources\shop;

use App\Client;
use App\v2\Models\ProductSku;
use App\SaleProduct;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sales = $this->partner_sales;

        $clients = collect(Client::find($this->clients))->push([
            'client_name' => 'Гость',
            'id' => -1
        ])->map(function ($item) {
            return collect($item)->only(['id', 'client_name']);
        })->map(function ($item) use ($sales) {
            $_sales = collect($sales)->filter(function ($i) use ($item){
               return $i['client_id'] === $item['id'];
            });
            $item['sale_total_sum'] = $_sales->reduce(function ($a, $c) {
                return $a + collect($c['products'])->reduce(function ($_a, $_c) {
                    return $_a + $_c['product_price'];
                }, 0);
            }, 0);

            $item['sale_total_bonuses'] = $item['sale_total_sum'] * 0.05;
            $_sales = $_sales->values()->map(function ($i) {
                $i['date'] = Carbon::parse($i['created_at'])->format('d.m.Y');
                $products = collect($i['products'])->map(function ($i) {
                    $product_id = $i['product_id'];
                    unset($i['product']);
                    $i['product'] = new OrderProductResource(SaleProduct::with('product')->whereKey($i['id'])->first());
                    $i['bonus'] = $i['product_price'] * 0.05;
                    return $i;
                });
                unset($i['products']);
                $i['products'] = collect($products)->map(function ($item) {
                    $item['product']['product_name'] = trim($item['product']['product_name']);
                    $item['product_image'] = $item['product']->product_image;
                    return collect($item)->only(['product_price', 'bonus', 'product']);
                });
                return $i;
            });

            unset($item['sales']);

            $item['sales'] = $_sales->map(function ($i) {
                return $i->only([
                    'date', 'products'
                ]);
            });

            return $item;
        })->filter(function ($i) {
            return $i['sale_total_sum'] > 0;
        });

        $sale_total_sum = $clients->reduce(function ($a, $c) {
            return $a + $c['sale_total_sum'];
        }, 0);

        $sale_total_bonus = $clients->reduce(function ($a, $c) {
            return $a + $c['sale_total_bonuses'];
        }, 0);


        return [
            'partner_name' => $this->client_name,
            'balance' => $sale_total_bonus,
            'clients_count' => count($clients),
            'total_sum' => $sale_total_sum,
            'clients' => $clients,
            'promocodes' => $this->promocodes->where('is_active', 1)
        ];
    }
}
