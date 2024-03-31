<?php

namespace App\Http\Controllers\api\Opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Repository\Opt\CartRepository;
use Exception;
use Illuminate\Http\Request;

class CartController extends BaseApiController
{

    public function __construct()
    {
        if (!auth()->user()) {
            return $this->respondError('Вы не авторизованы');
        }
    }

    public function get()
    {

    }

    /**
     * @throws Exception
     */
    public function create(Request $request)
    {
        $cartRepository = new CartRepository(auth()->user());
        $result = $cartRepository->addProductToCart($request->get('product_id'), $request->get('count'));
        return $this->respondSuccess([
            'cart' => $result,
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {

    }
}
