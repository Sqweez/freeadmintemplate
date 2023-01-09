<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromocodeResource;
use App\Product;
use App\Promocode;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{
    public function index() {
        return PromocodeResource::collection(Promocode::with('partner')->get());
    }

    public function store(Request $request) {
        $promocode = $request->get('promocode');
        $client_id = $request->get('client_id');
        $discount = $request->get('discount', null);
        $active_until = $request->get('active_until', null);
        try {
            $promocode = Promocode::create([
                'promocode' => $promocode,
                'client_id' => $client_id,
                'discount' => $discount,
                'is_active' => true,
                'active_until' => $active_until
            ]);
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
        return new PromocodeResource($promocode);
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
