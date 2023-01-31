<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Promocode\CreatePromocodeRequest;
use App\Http\Resources\PromocodeResource;
use App\Product;
use App\Promocode;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{
    public function index() {
        return PromocodeResource::collection(Promocode::with('partner')->get());
    }

    public function getTypes(): array {
        return collect(Promocode::TYPES)
            ->map(function ($value, $key) {
                return [
                    'id' => $key,
                    'name' => $value
                ];
            })
            ->values()
            ->toArray();
    }

    public function store(CreatePromocodeRequest $request) {
        try {
            $promocode = Promocode::create($request->validated())->refresh();
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
}
