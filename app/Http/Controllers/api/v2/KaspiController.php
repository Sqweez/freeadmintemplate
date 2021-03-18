<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Store;
use App\v2\Models\ProductSku;
use Illuminate\Http\Response;
use GuzzleHttp\Client;

class KaspiController extends Controller {
    public function getProductsXML() {
        $xmlContent =  $this->getXML($this->getProducts());
        $xmlContent = str_replace('&', '&amp;', $xmlContent);
        \Storage::disk('public')->put('kaspi\xml\kaspi_products.xml', $xmlContent);
        return (new Response('success', 200))
            ->header('Last-Modified', now()->toRfc822String());
    }

    public function getProducts() {
        $products = ProductSku::whereHas('product', function ($q) {
            return $q->where('is_kaspi_visible', true);
        })->with(['attributes'])->with(['product', 'product.attributes'])->with('product.manufacturer')->with(['batches' => function ($q) {
            return $q->where('store_id', 1)->where('quantity', '>', 0);
        }])->get()->sortBy('product_id');
        $stores = Store::whereTypeId(1)->get();
        return $products->map(function ($product) use ($stores) {
            return [
                'sku' => $product['id'],
                'product_name' => $product['manufacturer']['manufacturer_name'] . ' ' . $product['product_name'] . ' ' . collect($product['attributes'])->pluck('attribute_value')->join(' ') . ' ' . collect($product['product']['attributes'])->pluck('attribute_value')->join(' '),
                'brand' => $product['manufacturer']['manufacturer_name'],
                'price' => $product['product']['kaspi_product_price'],//['kaspi_produce_price'],
                'availabilities' => collect($stores)->map(function ($store) use ($product) {
                    return ['available' => collect($product['batches'])->filter(function ($item) use ($store) {
                        return $item['store_id'] === $store['id'];
                    })->count() > 0 ? 'yes' : 'no', 'storeId' => 'PP' . $store['id']];
                })];
        });
    }

    private function getXML($products) {
        $content = '<?xml version="1.0" encoding="utf-8"?>
                        <kaspi_catalog date="string"
                                      xmlns="kaspiShopping"
                                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                         xsi:schemaLocation="http://kaspi.kz/kaspishopping.xsd">
                           <company>IRON-ADDICTS KZ</company>
                            <merchantid>IronAddicts</merchantid>
                            <offers>';

        $content .= collect($products)->reduce(function ($a, $c) {
            return $a .
                '<offer sku="' . $c['sku'] .'">
                    <model>'. $c['product_name'] .'</model>
                    <brand>'. $c['brand'] .'</brand>
                    <availabilities>'.
                collect($c['availabilities'])->reduce(function ($_a, $_c) {
                    return $_a . '<availability available="'. $_c['available'] .'" storeId="'. $_c['storeId'] .'"/>';
                }, "")
                .'</availabilities>
                    <price>'. $c['price'] .'</price>
                 </offer>';
        }, "");

        $content .= '</offers></kaspi_catalog>';
        return $content;
    }

    public function getOrders() {
        $client = new Client();
      /*  $url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=1000&filter[orders][creationDate][$ge]=1611770400000&filter[orders][signatureRequired]=false&filter[orders][state]=PICKUP';
        return $url;*/
        $response = $client->get('https://kaspi.kz/shop/api/v2/orders', [
            'query' => [
                'page' => [
                    'number' => 0,
                    'size' => 1000
                ],
                'filter' => [
                    'orders' => [
                        'creationDate' => [
                            '$ge' => 1611770400000
                        ],
                        'signatureRequired' => false,
                        'state' => 'PICKUP'
                    ],
                ],
            ],
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => 'ULDaKPxr8fZzzxHBSj8HLc9YZ0x+VKhYdAd6vQ1NgnI='
            ]]
        );
        return $response->getBody();
    }
}
