<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManufacturerResource;
use App\Manufacturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return ManufacturerResource::collection(Manufacturer::query()->orderBy('manufacturer_name')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Manufacturer|Model
     */
    public function store(Request $request)
    {
        return Manufacturer::create($request->all());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Manufacturer $manufacturer
     * @return Manufacturer
     */
    public function update(Request $request, Manufacturer $manufacturer)
    {
        $manufacturer->update($request->all());
        return $manufacturer;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Manufacturer $manufacturer
     * @return void
     * @throws \Exception
     */
    public function destroy(Manufacturer $manufacturer)
    {
        $manufacturer->delete();
    }
}

