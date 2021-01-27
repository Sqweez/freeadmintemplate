<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Store;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class KaspiController extends Controller {
    public function getProductsXML(Request $request) {
        $products = ProductSku::whereHas('product', function ($q) {
            return $q->where('is_kaspi_visible', true);
        })->with(['attributes'])->with(['product', 'product.attributes'])->with('product.manufacturer')->with(['batches' => function ($q) {
                return $q->where('store_id', 1)->where('quantity', '>', 0);
            }])->get()->sortBy('product_id');

        $stores = Store::whereTypeId(1)->get();
        $productsXML = $products->map(function ($product) use ($stores) {
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

        $xmlContent =  $this->getXML($productsXML);
        $xmlContent = str_replace('&', '&amp;', $xmlContent);
        return (new Response($xmlContent, 200))->header('Content-Type', 'text/xml');

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
}
