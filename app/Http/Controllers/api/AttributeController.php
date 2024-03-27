<?php

namespace App\Http\Controllers\api;

use App\Attribute;
use App\v2\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Attribute[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Attribute::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Attribute|\Illuminate\Database\Eloquent\Model
     */
    public function store(Request $request)
    {
        return Attribute::create($request->all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Attribute $attribute
     * @return Attribute
     */
    public function update(Request $request, Attribute $attribute)
    {
        $attribute->update($request->all());
        return $attribute;
    }

    public function show()
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Attribute $attribute
     * @return void
     * @throws \Exception
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
    }

    public function getCurrencies(): JsonResponse
    {
        return $this->respondSuccess([
            'currencies' => Currency::all(),
        ]);
    }
}
