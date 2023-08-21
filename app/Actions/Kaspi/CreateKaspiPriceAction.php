<?php

namespace App\Actions\Kaspi;

use App\v2\Models\KaspiEntity;
use App\v2\Models\KaspiEntityStore;
use App\v2\Models\ProductSku;
use Illuminate\Http\Response;

class CreateKaspiPriceAction
{

    private KaspiEntity $kaspiEntity;

    public function __construct(KaspiEntity $kaspiEntity)
    {
        $this->kaspiEntity = $kaspiEntity;
    }

    public function handle(): Response
    {
        return $this->getKaspiProductXML();
    }

    public function getKaspiProductXML(): Response
    {
        $xmlContent = $this->getProductsXML();
        return $this->storeFile($xmlContent, 'kaspi\xml\kaspi_products_' . $this->kaspiEntity->id . '.xml');
    }

    private function storeFile($content, $path)
    {
        \Storage::disk('public')->put($path, $content);
        return (new Response('success', 200))
            ->header('Last-Modified', now()->toRfc822String());
    }

    private function getProductsXML()
    {
        $xmlContent =  $this->getXML($this->getProducts());
        return str_replace('&', '&amp;', $xmlContent);
    }

    private function getXML($products): string
    {
        $content = '<?xml version="1.0" encoding="utf-8"?>
                        <kaspi_catalog date="string"
                                      xmlns="kaspiShopping"
                                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                         xsi:schemaLocation="http://kaspi.kz/kaspishopping.xsd">
                           <company>'. $this->kaspiEntity->company_name .'</company>
                            <merchantid>'. $this->kaspiEntity->merchant_id .'</merchantid>
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

    private function getProducts()
    {
        $products = ProductSku::query()
            ->whereHas('product', function ($q) {
                return $q->whereHas('kaspi_price', function ($sQ) {
                    return $sQ
                        ->where('is_visible', true)
                        ->where('price', '!=', 0)
                        ->where('kaspi_entity_id', $this->kaspiEntity->id);
                });
            })
            ->with(['attributes', 'attributes.attribute_name'])
            ->with(['product', 'product.attributes', 'product.attributes.attribute_name'])
            ->with(['product.kaspi_price' => function ($q) {
                return $q->where('kaspi_entity_id', $this->kaspiEntity->id);
            }])
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

        $stores = KaspiEntityStore::whereKaspiEntityId($this->kaspiEntity->id)->get();
        return $products->map(function ($product) use ($stores) {
            return [
                'sku' => $product['id'],
                'product_name' => $product['manufacturer']['manufacturer_name'] . ' ' . $product['product_name'] . ' ' . collect($product['attributes'])->pluck('attribute_value')->join(' ') . ' ' . collect($product['product']['attributes'])->pluck('attribute_value')->join(' '),
                'brand' => $product['manufacturer']['manufacturer_name'],
                'base_name' => $product['product_name'],
                'price' => $product['product']['kaspi_price'][0]['price'],//['kaspi_produce_price'],
                'category_id' => $product['product']['category_id'],
                'attributes' => collect($product['attributes'])->mergeRecursive($product['product']['attributes']),
                'images' => $product['product']['product_images'],
                'availabilities' => collect($stores)->map(function ($store) use ($product) {
                    return ['available' => collect($product['batches'])->filter(function ($item) use ($store) {
                        return $item['store_id'] === $store['store_id'];
                    })->count() > 0 ? 'yes' : 'no', 'storeId' => 'PP' . ($store['kaspi_store_id'])];
                })];
        });

    }
}
