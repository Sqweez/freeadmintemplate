<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\Controller;
use App\Repository\Opt\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function create(Request $request)
    {
        $this->orderRepository->create(auth()->user(), $request->all());
    }
}
