<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\shop\RatingResource;
use App\RatingCriteria;
use App\Seller;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function getSellers() {
        return Seller::all();
    }

    public function createSeller(Request $request) {
        return Seller::create($request->all());
    }

    public function editSeller(Seller $seller, Request $request) {
        $seller->update($request->all());
        return $seller;
    }

    public function deleteSeller(Seller $seller) {
        $seller->delete();
    }

    public function getCriteria() {
        return RatingCriteria::all();
    }

    public function createCriteria(Request $request) {
        return RatingCriteria::create($request->all());
    }

    public function editCriteria(Request $request) {
        $criteria = RatingCriteria::find($request->get('id'));
        $criteria->update($request->all());
        return $criteria;
    }

    public function deleteCriteria($id) {
        $criteria = RatingCriteria::find($id);
        $criteria->delete();
    }


    public function vote(Request $request) {

    }

    public function getRating(Request $request) {
        return
            [
                'all_sellers' => RatingResource::collection(Seller::with(['rating'])->get()),
            ];
    }
}
