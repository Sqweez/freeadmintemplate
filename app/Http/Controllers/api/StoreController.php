<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Store;
use App\StoreType;
use App\v2\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection {
        $storeQuery = Store::query()
            ->with('type')
            ->with(['transactions'])
            ->with('city_name')
            ->when(auth()->user() && auth()->user()->isFranchise(), function ($query) {
                return $query->where('city_id', auth()->user()->store->city_id);
            });
        if ($request->has('store_id')) {
            $storeQuery->where('id', $request->get('store_id'));
        }
        return StoreResource::collection($storeQuery->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return StoreResource
     */
    public function store(Request $request): StoreResource {
        return new StoreResource(Store::create($request->all()));
    }

    public function indexStores() {
        return StoreResource::collection(Store::where('type_id', 1)->get());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Store $store
     * @return StoreResource
     */
    public function update(Request $request, Store $store)
    {
        $store->update($request->except(['balance', 'iron_balance', 'has_kaspi_terminal']));
        return new StoreResource($store);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Store  $store
     * @return void
     * @throws \Exception
     */
    public function destroy(Store $store)
    {
        $store->delete();
    }

    public function types() {
        return StoreType::all();
    }

    public function getCities(Request $request) {
        return City::query()
            ->when($request->has('country_id'), function ($q) use ($request) {
                return $q->where('country_id', $request->get('country_id'));
            })
            ->get();
    }

    public function getStoresForApplication() {
        $stores = Store::query()
            ->where('type_id', 1)
            ->select(['id', 'name', 'city_id'])
            ->with('city_name')
            ->get();

        return ['data' => $stores->map(function (Store $store) {
            return [
                'id' => $store->id,
                'city' => $store->city_name->name,
                'city_id' => $store->city_id,
                'name' => $store->name,
            ];
        })];
    }
}
