<?php

namespace App\Http\Controllers\api\Opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\CartResource;
use App\Repository\Opt\CartRepository;
use App\v2\Models\UserCartItem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends BaseApiController
{

    /**
     * @throws Exception
     */
    public function get(): JsonResponse
    {
        $cartRepository = new CartRepository(auth()->user());
        $cart = $cartRepository->getCart();
        return $this->respondSuccessNoReport([
            'cart' => CartResource::collection($cart['products']),
            'total' => $cart['total'],
            'stockChangedProducts' => $cart['stockChangedProducts']
        ]);
    }

    /**
     * @throws Exception
     */
    public function getTotal(): JsonResponse
    {
        return $this->get();
    }

    /**
     * @throws Exception
     */
    public function create(Request $request): JsonResponse
    {
        $cartRepository = new CartRepository(auth()->user());
        $cartRepository->addProductToCart($request->get('product_id'), $request->get('count'), $request->get('cart_product_id'));
        $cart = $cartRepository->getCart();
        return $this->respondSuccess([
            'cart' => CartResource::collection($cart['products']),
            'total' => $cart['total'],
            'message' => 'Товар успешно добавлен в корзину',
            'stockChangedProducts' => $cart['stockChangedProducts']
        ]);
    }

    /**
     * @throws Exception
     */
    public function delete(UserCartItem $product): JsonResponse
    {
        $cartRepository = new CartRepository(auth()->user());
        $cartRepository->removeProductFromCart($product);
        return $this->get();
    }

    public function update(Request $request)
    {

    }
}
