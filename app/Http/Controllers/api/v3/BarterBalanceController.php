<?php

namespace App\Http\Controllers\api\v3;

use App\Client;
use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\ClientResource;
use App\v2\Models\BarterBalance;
use Illuminate\Http\Request;

class BarterBalanceController extends BaseApiController
{
    public function store(Request $request)
    {
        $barterBalance = BarterBalance::query()
            ->create(
                $request->all() + ['user_id' => auth()->id()]
            );

        $client = Client::find($barterBalance->client_id);
        return $this->respondSuccess([
            'client' => new ClientResource($client),
        ]);
    }
}
