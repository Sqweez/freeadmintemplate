<?php

namespace App\Http\Controllers\api;

use App\Cart;
use App\CartProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\CartResource;
use App\Product;
use App\ProductBatch;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addCart(Request $request) {
        $user_token = $request->get('user_token');
        $product = $request->get('product');
        $count = $request->get('count');
        $type = $request->get('type') ?? 'web';
        $store_id = $request->cookie('store_id') ?? 1;
        if ($this->getCount($product, $count, $store_id) < $count) {
            return ['error' => 'Недостаточно товара на складе'];
        }
        $cart_id = $this->createCart($user_token, $type, $store_id);
        $this->createCartProduct($product, $cart_id, $count);
    }

    public function increaseCount(Request $request) {
        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = $request->cookie('store_id') ?? 1;
        $count = CartProduct::Cart($cart)->Product($product)->count();
        if ($this->getCount($product, $count + 1, $store_id) < $count) {
            return ['error' => 'Недостаточно товара на складе'];
        }

        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();
        $cartProduct->update(['count' => $cartProduct['count'] + 1]);
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
        $store_id = $request->cookie('store_id') ?? 1;
        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();
        if ($cartProduct['count'] === 1) {
            $cartProduct->delete();
        } else {
            $cartProduct->update(['count' => $cartProduct['count'] - 1]);
        }
    }

    public function getCart(Request $request) {
        $user_token = $request->get('user_token');
        return new CartResource(Cart::where('user_token', $user_token)->first());
    }

    private function createCart($user_token, $type, $store_id) {
        return Cart::create(['user_token' => $user_token, 'type' => $type, 'store_id' => $store_id])['id'];
    }

    private function getCount($product, $count, $store_id) {
        $quantity = ProductBatch::where('product_id', $product)->where('store_id', $store_id)->sum('quantity');
        return $quantity;
    }

    private function createCartProduct($product, $cart_id, $count) {
        CartProduct::create(['cart_id' => $cart_id, 'product_id' => $product, 'count' => $count]);
    }

    private function getBatch($product, $store_id) {
        return ProductBatch::where('product_id', $product)
            ->where('store_id', $store_id)
            ->where('quantity', '>=', 1)
            ->first();
    }
}
