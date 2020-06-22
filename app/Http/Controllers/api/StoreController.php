<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Store;
use App\StoreType;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $stores =
            $request->has('store_id')
                ?
                Store::where('id', $request->get('store_id'))->with('type')->get()
                :
                Store::with('type')->get();
        return StoreResource::collection($stores);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return StoreResource
     */
    public function store(Request $request)
    {
        return new StoreResource(Store::create($request->all()));
    }

    public function indexStores() {
        return StoreResource::collection(Store::where('type_id', 1)->get());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Store $store
     * @return StoreResource
     */
    public function update(Request $request, Store $store)
    {
        $store->update($request->all());
        return new StoreResource($store);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Store $store
     * @return void
     */
    public function destroy(Store $store)
    {
        $store->delete();
    }

    public function types() {
        return StoreType::all();
    }
}
