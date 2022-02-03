<?php

namespace App\Http\Controllers\api;

use App\Cart;
use App\CartProduct;
use App\Client;
use App\ClientTransaction;
use App\Http\Controllers\Services\ReportService;
use App\Sale;
use App\Store;
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
        //return ClientResource::collection(Client::with(['sales', 'transactions', 'city'])->get());
        return ClientResource::collection(
            Client::with(['sales', 'transactions', 'city', 'loyalty'])
                ->get()
        );
    }

    public function show(Client $client) {
        $client->load('sales');
        $client->load('transactions');
        $client->load('city');
        $client->load('loyalty');
        return [
            'client' => new ClientResource($client),
            'sales' => ReportService::getClientReports($client->id)
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return ClientResource
     */
    public function store(Request $request) {
        //return $request->all();
        $client = Client::create($request->all());
        return new ClientResource($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Client $client
     * @return \Illuminate\Support\Collection
     */
    public function update(Request $request, Client $client) {
        if (!$request->has('site')) {
            $_client = $request->only(
                ['client_name', 'client_card', 'client_phone', 'client_discount', 'is_partner', 'client_city', 'loyalty_id', 'job', 'instagram', 'photo', 'birth_date', 'gender']
            );
            $_client = collect($_client)->filter(function ($i) {
                return strlen($i) > 0;
            });
            $client->update($_client->toArray());
            return new ClientResource($client);
        } else {
            $_client = $request->except(['site', 'is_partner', 'client_discount', 'client_balance', 'city']);
            if (isset($_client['password'])) {
                $_client['password'] = Hash::make($_client['password']);
            }
            $_client = collect($_client)->filter(function ($i) {
                return strlen($i) > 0;
            });
            $store = Store::where('city', 'like', '%' . $_client['client_city'] .'%')->where('type_id', 1)->first();
            if (!$store) {
                $_client['client_city'] = -1;
            } else {
                $_client['client_city'] = $store['id'];
            }
            $client->update($_client->toArray());
            $store = Store::find($client['client_city']);
            if (!$store) {
                $client['city'] = null;
            } else {
                $client['city'] = $store['city'];
            }
            return collect($client)->only(['client_name', 'client_phone', 'address', 'client_city', 'email', 'id', 'client_discount', 'city']);
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
        $client->delete();
    }

    public function register(Request $request) {
        $user_token = $request->get('user_token');
        $phone = $request->get('phone');
        $password = $request->get('password');
        $name = $request->get('name');
        $city = $request->get('city');

        $client = Client::ofPhone($phone)->first();

        if ($client && (strlen($client->password) || strlen($client->user_token))) {
            return ['error' => 'Клиент с данным номер уже зарегистрирован!'];
        }

        if (!$client) {
            $client = Client::create([
                'client_name' => $name,
                'client_phone' => $phone,
                'client_card' => '',
                'client_discount' => 0,
                'password' => Hash::make($password),
                'address' => '',
                'user_token' => Str::random(60),
                'client_city' => $city,
                'email' => ''
                ]
            );
        } else {
            $client->update([
                'password' => Hash::make($password),
                'user_token' => Str::random(60),
                'client_name' => $name,
                'client_city' => $city
                ]);
        }

        $cart = Cart::ofUser($user_token)->first();

        if ($cart) {
            $cart->update(['user_token' => $client->user_token]);
        } else {
            $cart = Cart::create([
                'user_token' => $client->user_token,
                'type' => 'web',
                'store_id' => $city
            ]);
        }

        $this->addFreeBarForRegistration($cart);

        return collect($client)->only(['user_token']);
    }

    private function addFreeBarForRegistration(Cart $cart) {
        $bar_id = 2775;
        CartProduct::create([
            'cart_id' => $cart->id,
            'product_id' => $bar_id,
            'count' => 1
        ]);
    }

    public function getAuth(Request $request) {
        $user_token = $request->get('user_token');
        $client = Client::ofToken($user_token)->first();
        if (!$client) {
            return null;
        } else {
            $client['client_balance'] = $client->transactions->sum('amount');
            $store = Store::find($client['client_city']);
            if (!$store) {
                $client['city'] = null;
            } else {
                $client['city'] = $store['city'];
            }
            return collect($client)->only(['client_name', 'client_phone', 'address', 'client_city', 'email', 'id', 'client_discount', 'client_balance', 'is_partner', 'city']);
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
        $client = Client::ofToken($user_token)->with([
            'orders',
            'orders.items.product',
            'orders.items.product.product',
            'purchases',
            'purchases.products.product'
        ])->with(['orders.items' => function ($q) {
            return $q->has('product');
        }])->with(['purchases.products' => function ($q) {
            return $q->has('product');
        }])->first();
        if (!$client) {
            return response()->json(['error' => 'Клиент не найден'], 500);
        }


        $orders = $client->orders->filter(function ($q) {
            return count($q['items']) > 0;
        })->values();

        $sales = $client->purchases->filter(function ($q) {
            return count($q['products']) > 0;
        })->values();

        $orders = OrderResource::collection($orders);
        $sales = OrderResource::collection($sales);

        return collect($orders)->merge(collect($sales))->sortByDesc('created_at')->values()->all();
    }

    public function addBalance(Request $request, Client $client) {
        ClientTransaction::create(['client_id' => $client->id, 'user_id' => 1, 'amount' => $request->get('sum'), 'sale_id' => -1]);

        return new ClientResource($client);
    }

    public function getClientsWithoutSales(Request $request) {
        $start = $request->get('start');
        $finish = $request->get('finish');
        $clients = Client::with(['sales', 'transactions', 'city', 'loyalty'])
            ->get();

        $sales = Sale::query()
            ->reportDate([$start, $finish])
            ->where('client_id', '!=', -1)
            ->select(['id', 'client_id'])
            ->get();

        $sales = $sales->pluck('client_id')->values()->unique()->values()->all();
        $clients = $clients->filter(function ($client) use ($sales) {
           return !in_array($client['id'], $sales);
        });

        return ClientResource::collection($clients);
    }

    public function getClientAnalytics(Request $request) {
        $start = $request->get('start');
        $finish = $request->get('finish');
        $sales = Sale::where('client_id', '!=', -1)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->with('client')
            ->with('store')
            ->with('products')
            ->get();
        $sales = $sales->map(function ($sale) {
                $total_cost = collect($sale['products'])->reduce(function ($a, $c) {
                    return $a + $c['product_price'] - ($c['product_price'] * $c['discount'] / 100);
                }, 0);
                return [
                    'client' => $sale['client']['client_name'],
                    'store' => $sale['store']['name'],
                    'client_id' => $sale['client_id'],
                    'store_id' => $sale['store_id'],
                    'total_cost' => $total_cost
                ];
            })
            ->groupBy('client_id')
            ->map(function ($item, $key) {
                return [
                    'client' => $item[0]['client'],
                    'client_id' => $item[0]['client_id'],
                    'store_id' => $item[0]['store_id'],
                    'store' => $item[0]['store'],
                    'total_cost' => collect($item)->reduce(function ($a, $c) {
                        return $a + $c['total_cost'];
                    }, 0)
                ];
            })
            ->values();
        return [
            'top_clients_all' => $sales->sortByDesc('total_cost')->take(3)->values()->all(),
            'top_clients_store' => $sales->groupBy('store_id')->map(function ($items) {
                return collect($items)->sortByDesc('total_cost')->values()->take(3);
            })
        ];
    }

}
