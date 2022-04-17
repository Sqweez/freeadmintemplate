<?php

namespace App\Http\Controllers\api\v2;

use App\Category;
use App\Http\Controllers\Controller;
use App\Sale;
use App\SaleProduct;
use App\Store;
use App\v2\Models\ProductSku;
use Illuminate\Http\Response;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class KaspiController extends Controller {

    public function getKaspiProductsXML(): Response {
        $xmlContent = $this->getProductsXML();
        return $this->storeXML($xmlContent, 'kaspi\xml\kaspi_products.xml');
    }

    public function getForteProducts() {
        $xmlContent = $this->getForteProductsXML();
        return $this->storeXML($xmlContent, 'forte\xml\forte_products.xml');
    }

    public function getForteProductsXML() {
        $products = $this->getProducts();
        return $this->getForteXML($products);
    }

    public function getProductsXML() {
        $xmlContent =  $this->getKaspiXML($this->getProducts());
        $xmlContent = str_replace('&', '&amp;', $xmlContent);
        return $xmlContent;
    }

    private function storeXML($xmlContent, $path = 'kaspi\xml\kaspi_products.xml'): Response {
        \Storage::disk('public')->put($path, $xmlContent);
        return (new Response('success', 200))
            ->header('Last-Modified', now()->toRfc822String());
    }

    public function getProducts() {
        $products = ProductSku::query()
            ->whereHas('product', function ($q) {
                return $q->where('is_kaspi_visible', true);
            })
            ->with(['attributes', 'attributes.attribute_name'])
            ->with(['product', 'product.attributes', 'product.attributes.attribute_name'])
            ->with('product.manufacturer')
            ->with('product.product_images')
            ->with(['batches' => function ($q) {
                return $q->where('quantity', '>', 0);
            }])
            ->get()
            ->sortBy('product_id')
            ->values()
            ->map(function ($product) {
                $product['batches'] = collect($product['batches'])->map(function ($_product) {
                    return $_product;
                });
                return $product;
            });

        $stores = Store::whereTypeId(1)->get();
        return $products->map(function ($product) use ($stores) {
            return [
                'sku' => $product['id'],
                'product_name' => $product['manufacturer']['manufacturer_name'] . ' ' . $product['product_name'] . ' ' . collect($product['attributes'])->pluck('attribute_value')->join(' ') . ' ' . collect($product['product']['attributes'])->pluck('attribute_value')->join(' '),
                'forte_product_name' => $product['product_name'] . ' ' . $product['manufacturer']['manufacturer_name'] . ' ' . collect($product['attributes'])->pluck('attribute_value')->join(' ') . ' ' . collect($product['product']['attributes'])->pluck('attribute_value')->join(' '),
                'brand' => $product['manufacturer']['manufacturer_name'],
                'base_name' => $product['product_name'],
                'price' => $product['product']['kaspi_product_price'],//['kaspi_produce_price'],
                'category_id' => $product['product']['category_id'],
                'attributes' => collect($product['attributes'])->mergeRecursive($product['product']['attributes']),
                'images' => $product['product']['product_images'],
                'availabilities' => collect($stores)->map(function ($store) use ($product) {
                    return ['available' => collect($product['batches'])->filter(function ($item) use ($store) {
                        return $item['store_id'] === $store['id'];
                    })->count() > 0 ? 'yes' : 'no', 'storeId' => 'PP' . ($store['id'] == 14 ? '6' : $store['id'])];
                })];
        });
    }

    private function getKaspiXML($products) {
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

    private function getForteXML($products) {
        $categories = Category::with('subcategories')->get();
        $content = sprintf('<?xml version="1.0" encoding="UTF-8"?><yml_catalog date="%s">', now()->toRfc3339String());
        $content .= '<shop><name>IRON-ADDICTS</name>
        <company>IRON-ADDICTS</company>
        <url>https://iron-addicts.kz</url>
        <categories>';
        $content .= $categories->map(function ($category) {
            $subContent = sprintf('<category id="%s">%s</category>', $category->id, $category->category_name);
            foreach ($category['subcategories'] as $subcategory) {
                $subContent .= sprintf('<category id="%s" parentId="%s">%s</category>',
                    $subcategory['id'],
                    $category->id,
                    $subcategory['subcategory_name']
                );
            }
            return $subContent;
        })->join('');
        $content .= "</categories>";
        $content .= "<offers>";
        $content .= collect($products)->map(function ($product) {
            $subContent = sprintf('<offer id="%s">', $product['sku']);
            $preparedName = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $product['forte_product_name']);
            $subContent .= sprintf('<name>%s</name>', $preparedName);
            $subContent .= sprintf('<vendor>%s</vendor>', $product['brand']);
            $subContent .= sprintf('<price>%s</price>', $product['price']);
            foreach ($product['attributes'] as $attribute) {
                $subContent .= sprintf('<param name="%s">%s</param>', $attribute['attribute_name']['attribute_name'], $attribute['attribute_value']);
            }
            $subContent .= sprintf('<categoryId>%s</categoryId>', $product['category_id']);
            foreach ($product['images'] as $image) {
                $preparedURL = url('/') . \Storage::url($image['image']);
                $subContent .= sprintf('<picture>%s</picture>', $preparedURL);
            }
            $subContent .= '</offer>';
            return $subContent;
        })->join('');
        $content .= "</offers>";
        $content .= "</shop>";
        $content .= "</yml_catalog>";
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
                                '$ge' => now()->subHour()->getPreciseTimestamp(3)
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
        return $response->getStatusCode();
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
