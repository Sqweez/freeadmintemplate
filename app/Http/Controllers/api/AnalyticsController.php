<?php

namespace App\Http\Controllers\api;

use App\AnalyticSearch;
use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function storeSearch(Request $request) {
        $user_token = $request->get('user_token');
        $search = $request->get('search');

        $client = Client::ofToken($user_token)->first();

        $client_id = $client ? $client['id'] : -1;

        return AnalyticSearch::create([
            'search' => $search,
            'client_id' => $client_id
        ]);
    }
}
