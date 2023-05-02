<?php

namespace App\Http\Controllers\api;

use App\Actions\Promocode\CheckPromocodeAction;
use App\Http\Resources\PromocodeResource;
use App\Promocode;
use Illuminate\Http\Request;

class PromocodeController extends BaseApiController
{
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
        ];
    }

    public function store(Request $request) {
        try {
            $promocode = Promocode::create($request->all())->refresh();
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

    public function checkPromocode(Request $request) {
        $cart = $request->get('cart');
        $code = $request->get('promocode');
        $promocodeAction = CheckPromocodeAction::i();
        $promocode = $promocodeAction->handle($code, $cart);
        if ($promocode['success']) {
            return $this->respondSuccess([]);
        } else {
            return $this->respondError($promocode['message']);
        }
    }
}
