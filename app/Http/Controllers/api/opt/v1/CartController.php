<?php

namespace App\Http\Controllers\api\Opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Repository\Opt\CartRepository;
use App\v2\Models\ProductSku;
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
        return $this->respondSuccess([
            'cart' => $cartRepository->getCart(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function create(Request $request): JsonResponse
    {
        $cartRepository = new CartRepository(auth()->user());
        $result = $cartRepository->addProductToCart($request->get('product_id'), $request->get('count'));
        return $this->respondSuccess([
            'cart' => $result,
            'user' => auth()->user(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function delete(ProductSku $product)
    {
        $cartRepository = new CartRepository(auth()->user());
        $cartRepository->removeProductFromCart($product->id);
        return $this->respondSuccess();
    }

    public function update(Request $request)
    {

    }
}
