<?php

namespace App\Http\Controllers\api;

use App\Actions\Promocode\CheckPromocodeAction;
use App\Client;
use App\Http\Resources\PromocodeResource;
use App\Promocode;
use App\Repository\PromocodeRepository;
use App\Store;
use App\v2\Models\ClientPromocode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromocodeController extends BaseApiController
{
    private $promocodeRepository;

    public function __construct()
    {
        $this->promocodeRepository = new PromocodeRepository();
    }

    public function index() {
        return PromocodeResource::collection(Promocode::with('partner')->get());
    }

    public function getTypes(): array {

        return [
            'types' => collect(Promocode::TYPES)
                ->map(function ($value, $key) {
                    return [
                        'id' => $key,
                        'name' => $value
                    ];
                })
                ->values()
                ->toArray(),
            'conditions' => collect(Promocode::CONDITIONS)
                ->map(function ($value, $key) {
                    return [
                        'id' => $key,
                        'name' => $value
                    ];
                })
                ->values()
                ->toArray(),
            'purposes' => collect(Promocode::PURPOSES)
                ->map(function ($value, $key) {
                    return [
                        'id' => $key,
                        'name' => $value
                    ];
                })
                ->values()
                ->toArray(),
            'cascades' => collect(Promocode::CASCADES)
                ->map(function ($value, $key) {
                    return [
                        'id' => $key,
                        'name' => $value
                    ];
                })
                ->values()
                ->toArray(),
            'apply_types' => collect(Promocode::APPLY_TYPES)
                ->map(function ($value, $key) {
                    return [
                        'id' => $key,
                        'name' => $value
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }

    public function store(Request $request) {
        try {

            $payload = $request->all();

            if ($payload['promocode_apply_type_id'] == __hardcoded(2)) {
                $payload['promocode'] = \Str::upper(\Str::random(30));
            }

            $promocode = Promocode::create($payload)->refresh();
            return new PromocodeResource($promocode);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => 'Данный промокод уже существует!'
            ])->withException($exception)->setStatusCode(500);
        }


    }

    public function update(Request $request, Promocode $promocode): PromocodeResource {
        $promocode->update($request->all());
        return new PromocodeResource($promocode->refresh());
    }


    public function destroy(Promocode $promocode) {
        $promocode->delete();
    }

    public function searchPromocode($promocode) {
        $_promocode = Promocode::query()
            ->where('promocode', $promocode)
            ->active()
            ->first();
        if (!$_promocode) {
            return response()->json([
                'error' => 'Промокод не найден или неактивен'
            ])->setStatusCode(404);
        }
        return new PromocodeResource($_promocode);
    }

    public function checkPromocode(Request $request): JsonResponse {
        $cart = $request->get('cart');
        $code = $request->get('promocode');
        $promocode_id = $request->get('promocode_id');
        if ($promocode_id) {
            $promocode = Promocode::findOrFail($promocode_id);
            $code = $promocode->promocode;
        }
        $promocode = CheckPromocodeAction::i()
            ->handle($code, $cart);
        return $this->respondSuccess([
            'success' => $promocode['success'],
            'cart' => $promocode['cart'] ?? null,
            'promocode' => $promocode['promocode'] ?? null,
        ], $promocode['message']);
    }

    public function setClients(Promocode $promocode, Request $request)
    {
        $codes = $request->get('codes', []);
        foreach ($codes as $code) {
            ClientPromocode::firstOrCreate([
                'card_code' => $code,
                'client_id' => null,
            ],[
                'promocode_id' => $promocode->id,
            ]);
        }
    }

    public function getAvailableStocks(Request $request): JsonResponse
    {
        $client = Client::find($request->get('client_id'));
        $store = Store::find($request->get('store_id'));
        return $this->respondSuccess([
            'stocks' => $this->promocodeRepository->getAvailableStocks($client, $store)
        ]);
    }
}
