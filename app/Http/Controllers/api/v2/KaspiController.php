<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Sale;
use App\SaleProduct;
use App\Store;
use App\v2\Models\ProductSku;
use Illuminate\Http\Response;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class KaspiController extends Controller {

    public function getKaspiProductsXML() {
        $xmlContent = $this->getKaspiProductsXML();
        return $this->storeXML($xmlContent, 'kaspi\xml\kaspi_product.xml');
    }

    public function getProductsXML() {
        $xmlContent =  $this->getXML($this->getProducts());
        $xmlContent = str_replace('&', '&amp;', $xmlContent);
        return $xmlContent;
    }

    private function storeXML($xmlContent, $path = 'kaspi\xml\kaspi_product.xml') {
        \Storage::disk('public')->put($path, $xmlContent);
        return (new Response('success', 200))
            ->header('Last-Modified', now()->toRfc822String());
    }

    public function getProducts() {
        $products = ProductSku::query()
            ->whereHas('product', function ($q) {
                return $q->where('is_kaspi_visible', true);
            })
            ->with(['attributes'])
            ->with(['product', 'product.attributes'])
            ->with('product.manufacturer')
            ->with(['batches' => function ($q) {
                return $q->where('quantity', '>', 0);
            }])
            ->get()
            ->sortBy('product_id')
            ->values()
            ->map(function ($product) {
                $product['batches'] = collect($product['batches'])->map(function ($_product) {
                    if ($_product['store_id'] === 6) {
                        $_product['store_id'] = 1;
                    }
                    return $_product;
                });
                return $product;
            });

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

    public function getAnalytics(Request $request) {
        $start = $request->get('start');
        $finish = $request->get('finish');
        $sales = SaleProduct::whereHas('sale', function ($query) use ($start, $finish) {
            return $query->wherePaymentType(Sale::KASPI_PAYMENT_TYPE)
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $finish);
        }
        )->get()->groupBy('product_id')->map(function ($items) {
            return [
                'count' => count($items),
                'product_id' => $items[0]['product_id']
            ];
        })->values()->sortByDesc('count')->values();
        $product_ids = $sales->pluck('product_id');
        $products = ProductSku::whereIn('id', $product_ids)
            ->with('product', 'product.manufacturer', 'attributes', 'product.attributes', 'product.category')
            ->get();
        return $sales->filter(function ($sale) use ($products) {
            $product = collect($products)->filter(function ($p) use ($sale) {
                return $p['id'] === $sale['product_id'];
            })->first();
            return isset($product) && isset($product['product']);
        })
            ->map(function ($sale) use ($products) {
                $product = collect($products)->filter(function ($p) use ($sale) {
                    return $p['id'] === $sale['product_id'];
                })->first();
                $_product = $product['product'];
                return [
                    'count' => $sale['count'],
                    'product_id' => $sale['product_id'],
                    'product_name' => $_product['product_name'],
                    'category' => $_product['category']['category_name'],
                    'manufacturer' => $_product['manufacturer']['manufacturer_name'],
                    'category_id' => $_product['category_id'],
                    'attributes' => collect($_product['attributes'])->map(function ($a) {
                        return $a['attribute_value'];
                    })->merge(collect($product['attributes'])->map(function ($a) {
                        return $a['attribute_value'];
                    }))->join(', ')
                ];
            })->values()->all();
    }
}
