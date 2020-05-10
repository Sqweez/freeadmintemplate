<?php

namespace App\Http\Controllers\api;

use App\Cart;
use App\CartProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\CartResource;
use App\Product;
use App\ProductBatch;
use Illuminate\Http\Request;

class CartController extends Controller {
    public function addCart(Request $request) {
        $user_token = $request->get('user_token');
        $product = $request->get('product');
        $count = $request->get('count');
        $type = $request->get('type') ?? 'web';
        $store_id = $request->get('store_id') ?? 1;
        if ($this->getCount($product, $store_id) < $count) {
            return ['error' => 'Недостаточно товара на складе'];
        }
        $cart_id = $this->createCart($user_token, $type, $store_id);
        $this->createCartProduct($product, $cart_id, $count);
        return new CartResource(Cart::find($cart_id));
    }

    public function increaseCount(Request $request) {
        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = $request->get('store_id') ?? 1;
        $count = $request->get('count');
        if ($this->getCount($product, $store_id) <= $count) {
            return ['error' => 'Недостаточно товара на складе'];
        }

        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();
        $cartProduct->update(['count' => $cartProduct['count'] + 1]);
        return new CartResource(Cart::find($cart));
    }

    public function deleteCart(Request $request) {

        $cart = $request->get('cart');

        $product = $request->get('product');

        CartProduct::Cart($cart)->Product($product)->delete();

        return new CartResource(Cart::find($cart));

    }

    public function decreaseCount(Request $request) {

        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = $request->get('store_id') ?? 1;

        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();

        if (intval($cartProduct['count']) === 1) {
            $cartProduct->delete();
        } else {
            $cartProduct->update(['count' => $cartProduct['count'] - 1]);
        }

        return new CartResource(Cart::find($cart));
    }

    public function getCart(Request $request) {
        $user_token = $request->get('user_token');
        $cart = Cart::where('user_token', $user_token)->first() ?? null;
        return new CartResource($cart);
    }

    private function createCart($user_token, $type, $store_id) {
        $cart = Cart::where('user_token', $user_token)->first();
        if (!$cart) {
            return Cart::create(['user_token' => $user_token, 'type' => $type, 'store_id' => $store_id])['id'];
        } else {
            return $cart['id'];
        }
    }

    private function getCount($product, $store_id) {
        $quantity = ProductBatch::where('product_id', $product)->where('store_id', $store_id)->sum('quantity');
        return $quantity;
    }

    private function createCartProduct($product, $cart_id, $count) {
        $cartProduct = CartProduct::Cart($cart_id)->Product($product)->first();
        if (!$cartProduct) {
            CartProduct::create(['cart_id' => $cart_id, 'product_id' => $product, 'count' => $count]);
        } else {
            $cartProduct->update(['count' => $cartProduct['count'] + $count]);
        }
    }

    private function getBatch($product, $store_id) {
        return ProductBatch::where('product_id', $product)->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
    }
}
