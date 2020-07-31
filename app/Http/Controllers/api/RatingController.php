<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\shop\RatingResource;
use App\RatingCriteria;
use App\Seller;
use App\SellerRating;
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
        $user_token = $request->get('user_token');
        $seller_id = $request->get('seller_id');
        $criteria_id = $request->get('criteria_id');

        $previousRating = SellerRating::where('user_token', $user_token)->where('seller_id', $seller_id)->where('criteria_id', $criteria_id)->first();

        if ($previousRating) {
            $previousRating->delete();
        }

        SellerRating::create($request->all());
    }

    public function getRating(Request $request) {
        return
            [
                'all_sellers' => RatingResource::collection(Seller::with(['rating'])->get()),
            ];
    }
}
