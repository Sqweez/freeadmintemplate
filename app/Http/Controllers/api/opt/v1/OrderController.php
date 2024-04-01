<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\OrderHistoryResource;
use App\Repository\Opt\OrderRepository;
use App\v2\Models\WholesaleClient;
use App\v2\Models\WholesaleOrder;
use App\v2\Models\WholesaleOrderProduct;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $this->orderRepository->create(auth()->user(), $request->all());
        return $this->respondSuccess([
            'message' => 'Ваш заказ успешно создан, с вами свяжется менеджер уточенения деталей'
        ]);
    }

    public function getOrderProductsHistory(WholesaleOrder $order)
    {
        return $this->respondSuccessNoReport([
           'order' =>
               $order->products()->with([
                   'product.product',
                   'product.product.attributes',
                   'product.attributes',
                   'product.product.product_thumbs',
                   'product.product.manufacturer'
               ])
               ->get()
               ->groupBy('product.product_id')
               ->map(function ($items) {
                   /* @var WholesaleOrderProduct $product */
                   $product = $items->first();
                   return [
                       'id' => $product->id,
                       'product_image' => $product->product->product->retrieveProductThumb(),
                       'product_name' => $product->product->product->product_name,
                       'manufacturer' => $product->product->product->manufacturer->manufacturer_name,
                       'product_sub_name' => $product->product->product->attributes->pluck('attribute_value')->join(' '),
                       'total_price' => $items->sum('price'),
                       'link' => $product->product->product->getOptLink(),
                       'items' => $items
                            ->groupBy('product_id')
                            ->map(function ($items, $product_id) {
                                /* @var WholesaleOrderProduct $product */
                                $product = $items->first();
                                return [
                                    'id' => $product->id,
                                    'count' => $items->count(),
                                    'total_price' => $items->sum('price'),
                                    'type' => $product->product->attributes->pluck('attribute_value')->join(' '),
                                ];
                            })
                            ->values()

                   ];
               })
                ->values()
        ]);
    }

    public function getOrdersHistory(): \Illuminate\Http\JsonResponse
    {
        /* @var WholesaleClient $client */
        $client = auth()->user();
        return $this->respondSuccessNoReport([
            'orders' => OrderHistoryResource::collection(
                $this->orderRepository->getOrdersHistory($client)
            ),
        ]);
    }
}
