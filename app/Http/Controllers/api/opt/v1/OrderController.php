<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\OrderHistoryResource;
use App\Http\Resources\Opt\OrderProductsHistoryResource;
use App\Repository\Opt\OrderRepository;
use App\v2\Models\WholesaleOrder;
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
           'order' => OrderProductsHistoryResource::collection(
               $order->products()->with([
                   'product.product',
                   'product.product.attributes',
                   'product.attributes',
                   'product.product.product_thumbs',
                   'product.product.manufacturer'
               ])
               ->get()
           )
        ]);
    }

    public function getOrdersHistory()
    {
        return $this->respondSuccessNoReport([
            'orders' => OrderHistoryResource::collection($this->orderRepository->getOrdersHistory(auth()->user())),
        ]);
    }
}
