<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Manufacturer[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Manufacturer::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Manufacturer::create($request->all());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Manufacturer  $manufacturer
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
     * @param  \App\Manufacturer  $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manufacturer $manufacturer)
    {
        $manufacturer->delete();
    }
}
