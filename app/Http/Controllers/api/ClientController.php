<?php

namespace App\Http\Controllers\api;

use App\Cart;
use App\CartProduct;
use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\shop\OrderResource;
use App\Http\Resources\shop\SaleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index() {
        return ClientResource::collection(Client::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return ClientResource
     */
    public function store(Request $request) {
        $client = Client::create($request->all());
        return new ClientResource($client);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Client $client
     * @return ClientResource
     */
    public function update(Request $request, Client $client) {
        if (!$request->has('site')) {
            $_client = $request->only(['client_name', 'client_card', 'client_phone', 'client_discount']);
            $_client = collect($_client)->filter(function ($i) {
                return strlen($i) > 0;
            });
            $client->update($_client->toArray());
            return new ClientResource($client);
        }
        else {
            $_client = $request->except('site');
            if (isset($_client['password'])) {
                $_client['password'] = Hash::make($_client['password']);
            }
            $_client = collect($_client)->filter(function ($i) {
                return strlen($i) > 0;
            });
            $client->update($_client->toArray());
            return collect($client)->only(['client_name', 'client_phone', 'address', 'city', 'email', 'id', 'client_discount']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Client $client
     * @return void
     * @throws \Exception
     */
    public function destroy(Client $client) {
        // @TODO:
        // 1.Удалить все связанные с клиентом вещи
        $client->delete();
    }

    public function register(Request $request) {
        $user_token = $request->get('user_token');
        $phone = $request->get('phone');
        $password = $request->get('password');

        $client = Client::ofPhone($phone)->first();

         if ($client && (strlen($client->password) || strlen($client->user_token))) {
             return [
                 'error' => 'Клиент с данным номер уже зарегистрирован!'
             ];
         }

        if (!$client) {
            $client = Client::create(['client_name' => "", 'client_phone' => $phone, 'client_card' => '', 'client_discount' => 0, 'password' => Hash::make($password), 'address' => '', 'user_token' => Str::random(60), 'email' => '']);
        } else {
            $client->update(['password' => Hash::make($password), 'user_token' => Str::random(60),]);
        }

        $cart = Cart::ofUser($user_token)->first();

        if ($cart) {
            $cart->update(['user_token' => $client->user_token]);
        }

        return collect($client)->only(['user_token']);
    }

    public function getAuth(Request $request) {
        $user_token = $request->get('user_token');
        $client = Client::ofToken($user_token)->first();
        if (!$client) {
            return null;
        } else {
            return collect($client)->only(['client_name', 'client_phone', 'address', 'city', 'email', 'id', 'client_discount']);
        }
    }

    public function login(Request $request) {
        $user_token = $request->get('user_token');
        $phone = $request->get('phone');
        $password = $request->get('password');

        $client = Client::ofPhone($phone)->first();

        if (!$client) {
            return ['error' => 'Пользователь с данным номером не найден!'];
        }
        if (!Hash::check($password, $client->password)) {
            return ['error' => 'Неверный пароль!'];
        }

        CartController::mergeCarts($user_token, $client->user_token);

        return collect($client)->only('user_token');
    }


    public function getOrders(Request $request) {
        $user_token = $request->get('user_token');
        $client = Client::ofToken($user_token)->first();
        if (!$client) {
            return response()->json(['error' => 'Клиент не найден']);
        }

        $orders = OrderResource::collection($client->orders->where('status', '!=', 1));
        $sales = OrderResource::collection($client->purchases);
        
        return collect($orders)->merge(collect($sales));
    }
}
