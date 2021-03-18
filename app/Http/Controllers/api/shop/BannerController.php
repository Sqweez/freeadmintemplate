<?php

namespace App\Http\Controllers\api\shop;

use App\Banner;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\FileService;
use App\Http\Resources\shop\BannerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->has('site')) {
            $banners = Banner::where('is_active', true)->get();
            return BannerResource::collection(
                $banners->sortBy('order')->map(function($i, $key) use ($banners) {
                    $count = $banners->count();
                    $orders = collect(range(1, $count))->map(function ($number) {
                        return $number;
                    });

                    $_orders = $banners->pluck('order')->unique()->filter(function ($i) {
                        return $i !== 0;
                    });

                    $diff = $orders->diff($_orders)->values();

                    if ($i->order === 0) {
                        $i->order = $diff->first();
                    }
                    return $i;
                })->sortBy('order')
            );
        }
        return BannerResource::collection(Banner::all());
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return BannerResource
     */
    public function store(Request $request)
    {
        $banner = Banner::create($request->all());
        return new BannerResource($banner);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return BannerResource
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update($request->all());
        return new BannerResource($banner);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        Storage::delete('public/' . $banner->image);
        $banner->delete();
    }
}
