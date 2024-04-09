<?php

namespace App\Service\Documents\Products;

use App\v2\Models\ProductSku;
use App\v2\Models\WholesaleOrder;
use App\v2\Models\WholesaleOrderProduct;

class WholesaleOrderProductsResolver implements ProductsResolverInterface
{
    protected WholesaleOrder $order;

    public function __construct(WholesaleOrder $order)
    {
        $this->order = $order;
    }

    public function resolve(): array
    {
        $this->order->load('products.product.product.manufacturer');
        $this->order->load('products.product.product.attributes');
        $this->order->load('products.product.attributes');
        $orderProducts = $this->order->products
            ->groupBy('product_id')
            ->map(function ($items) {
                return $items
                    ->groupBy('discount')
                    ->map(function ($items) {
                        return [
                            'product' => $items->first(),
                            'count' => $items->count()
                        ];
                    })
                    ->values();
            })
            ->values()
            ->flatten(1);
        \Log::info('Products', $orderProducts->toArray());
        $products = [];
        $totalCount = 0;
        $totalPrice = 0;
        /* @var WholesaleOrderProduct $product */
        foreach ($orderProducts as $key => $product) {
            $totalCount += $product['count'];
            $currentPrice = $this->getPrice($product['product']);
            $totalPrice += $currentPrice * $product['count'];
            $products[] = [
                'index' => $key + 1,
                'name' => $this->getProductName($product['product']['product']),
                'count' => $product['count'],
                'price_per_product' => $this->formatPrice($currentPrice),
                'total_price' => $this->formatPrice($currentPrice * $product['count']),
            ];
        }
        return [
            'products' => $products,
            'totalPrice' => $this->formatPrice($totalPrice),
            'totalCount' => $this->formatPrice($totalCount),
            'totalCountText' => number2string($totalCount),
            'totalPriceText' => number2string($totalPrice) . $this->order->currency->getDeclension($totalPrice),
        ];
    }

    private function getProductName(ProductSku $productSku): string
    {
        return sprintf(
            "%s %s %s %s",
            $productSku->product->manufacturer->manufacturer_name,
            $productSku->product->product_name,
            $productSku->product->attributes->pluck('attribute_value')->join(' '),
            $productSku->attributes->pluck('attribute_value')->join(' '),
        );
    }

    private function getPrice(WholesaleOrderProduct $product): int
    {
        return $product->getFinalPriceAttribute();
    }

    private function formatPrice($price): string
    {
        return number_format($price, 2, ',', ' ');
    }
}
