<?php

namespace App\Http\Controllers\api;

use App\Goal;
use App\GoalPart;
use App\GoalPartProducts;
use App\Http\Controllers\Controller;
use App\Http\Resources\GoalResource;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        if ($request->has('home')) {
            return Goal::all();
        }

        return Goal::with(['parts'])->whereHas('parts', function ($q) {
            return $q->where('products', '!=', null);
        })->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $image = $request->get('image');
        $mobile_image = $request->get('mobile_image');

        $goal = Goal::create([
            'name' => $name,
            'image' => $image,
            'mobile_image' => $mobile_image
        ]);

        $parts = $request->get('parts') ?? [];

        $this->createParts($parts, $goal);

        return Goal::with(['parts'])->whereHas('parts', function ($q) {
            return $q->where('products', '!=', null);
        })->whereKey($goal->id)->first();

    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return GoalResource
     */
    public function show($slug)
    {
        return new GoalResource(Goal::where('slug', $slug)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Goal $goal
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function update(Request $request, Goal $goal)
    {
        $_goal = $request->only(['image', 'name', 'mobile_image']);
        $goal->update($_goal);
        $goal->parts()->delete();
        $parts = $request->get('parts') ?? [];
        $this->createParts($parts, $goal);
        return Goal::with(['parts'])->whereHas('parts', function ($q) {
            return $q->where('products', '!=', null);
        })->whereKey($goal->id)->first();
    }

    private function createParts($parts, Goal $goal) {
        foreach ($parts as $part) {
            $goal_part = GoalPart::create([
                'goal_id' => $goal->id,
                'category_id' => $part['category_id'],
                'subcategory_id' => $part['subcategory_id'],
                'description' => $part['description'],
                'name' => $part['name'],
                'products' => $part['products']
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Goal $goal)
    {
        $goal->delete();
        return null;
    }
}
