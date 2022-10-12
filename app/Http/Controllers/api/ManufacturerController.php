<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;
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
     * @param CreateManufacturerRequest $request
     * @return ManufacturerResource
     */
    public function store(CreateManufacturerRequest $request)
    {
        $data = $request->validated();
        if (isset($data['manufacturer_img'])) {
            $data['manufacturer_img'] = $request->file('manufacturer_img')->store('public/manufacturers');
        }
        return ManufacturerResource::make(Manufacturer::create($data));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateManufacturerRequest $request
     * @param Manufacturer $manufacturer
     * @return ManufacturerResource
     */
    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer): ManufacturerResource {
        $data = $request->validated();
        if (isset($data['manufacturer_img'])) {
            try {
                \Storage::delete($manufacturer->manufacturer_img);
            } catch (\Exception $exception) {
                \Log::error($exception->getMessage());
            } finally {
                $data['manufacturer_img'] = $request->file('manufacturer_img')->store('public/manufacturers');
            }
        }
        $manufacturer->update($data);
        return ManufacturerResource::make($manufacturer);
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

