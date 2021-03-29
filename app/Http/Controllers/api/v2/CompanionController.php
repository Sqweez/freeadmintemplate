<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Store;
use Illuminate\Http\Request;

class CompanionController extends Controller
{
    public function addBalance(Store $store, Request $request) {
        $store->transactions()->create([
            'transaction_sum' => $request->get('sum'),
            'user_id' => $request->header('user_id'),
        ]);
        $_store = $store->fresh();
        return new StoreResource($_store);
    }
}
