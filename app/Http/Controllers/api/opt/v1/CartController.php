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
        return $this->respondSuccessNoReport([
            'cart' => CartResource::collection($cartRepository->getCart()),
        ]);
    }

    /**
     * @throws Exception
     */
    public function getTotal()
    {
        $cartRepository = new CartRepository(auth()->user());
        return [
            'total' => $cartRepository->getTotal(),
        ];
    }

    /**
     * @throws Exception
     */
    public function create(Request $request): JsonResponse
    {
        $cartRepository = new CartRepository(auth()->user());
        $cartRepository->addProductToCart($request->get('product_id'), $request->get('count'));
        $result = $cartRepository->getTotal();
        return $this->respondSuccess([
            'cart' => $result,
            'message' => 'Корзина обновлена'
        ]);
    }

    /**
     * @throws Exception
     */
    public function delete(UserCartItem $product)
    {
        $cartRepository = new CartRepository(auth()->user());
        $cartRepository->removeProductFromCart($product);
        $result = $cartRepository->getTotal();
        return $this->respondSuccess([
            'cart' => $result,
            'message' => 'Корзина обновлена'
        ]);
    }

    public function update(Request $request)
    {

    }
}
