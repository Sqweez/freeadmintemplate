<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\OrderHistoryResource;
use App\Repository\Opt\OrderRepository;
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

    public function getOrdersHistory()
    {
        return $this->respondSuccessNoReport([
            'orders' => OrderHistoryResource::collection($this->orderRepository->getOrdersHistory(auth()->user())),
        ]);
    }
}
