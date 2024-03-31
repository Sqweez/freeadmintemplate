<?php

namespace App\Http\Controllers\api\Opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Repository\Opt\CartRepository;
use Illuminate\Http\Request;

class CartController extends BaseApiController
{
    public function get()
    {

    }

    public function create(Request $request)
    {
        $cartRepository = new CartRepository(auth()->user());
        $result = $cartRepository->addToCart(...$request->all());
        return $this->respondSuccess([
            'cart' => $result,
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {

    }
}
