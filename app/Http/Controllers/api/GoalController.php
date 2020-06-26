<?php

namespace App\Http\Controllers\api;

use App\Goal;
use App\GoalPart;
use App\GoalPartProducts;
use App\Http\Controllers\Controller;
use App\Http\Resources\GoalResource;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->has('home')) {
            return Goal::all();
        }
        return GoalResource::collection(Goal::with('parts')->get());
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
     * @param  \Illuminate\Http\Request  $request
     * @return GoalResource
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $image = $request->get('image');

        $goal = Goal::create([
            'name' => $name,
            'image' => $image,
        ]);

        $parts = $request->get('parts') ?? [];

        $this->createParts($parts, $goal);

        return new GoalResource($goal);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return GoalResource
     */
    public function update(Request $request, Goal $goal)
    {
        $_goal = $request->only(['image', 'name']);
        $goal->update($_goal);
        $goal_parts = $goal->parts;
        collect($goal_parts)->map(function ($i) {
            GoalPartProducts::where('goal_part_id', $i['id'])->delete();
            $i->delete();
        });

        $parts = $request->get('parts') ?? [];

        $this->createParts($parts, $goal);

        return new GoalResource($goal);
    }

    private function createParts($parts, Goal $goal) {
        foreach ($parts as $part) {
            $goal_part = GoalPart::create([
                'goal_id' => $goal->id,
                'category_id' => $part['category_id'],
                'subcategory_id' => $part['subcategory_id'],
                'description' => $part['description'],
                'name' => $part['name']
            ]);

            foreach ($part['products'] as $product) {
                GoalPartProducts::create([
                    'goal_part_id' => $goal_part->id,
                    'product_id' => $product
                ]);
            }
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
    }
}
