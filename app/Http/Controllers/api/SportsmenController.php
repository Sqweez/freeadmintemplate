<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Sportsmen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SportsmenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        if ($request->has('shop')) {
            $sportsmen = Sportsmen::all();
            return collect($sportsmen)->map(function ($i) {
                $i->image = url('/') . Storage::url($i->image);
                $i->nickname = explode('/', $i->instagram)[3];
                return $i;
            });
        }

        return Sportsmen::all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Sportsmen::create($request->all());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Sportsmen $sportsmen
     * @return Sportsmen
     */
    public function update(Request $request, $id)
    {
        $sportsmen = Sportsmen::find($id);
        $sportsmen->update($request->all());
        return $sportsmen;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return void
     */
    public function destroy($id)
    {
        Sportsmen::find($id)->delete();
    }
}
