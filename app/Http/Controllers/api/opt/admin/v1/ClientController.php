<?php

namespace App\Http\Controllers\api\opt\admin\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\ClientsResource;
use App\v2\Models\WholesaleClient;
use Illuminate\Http\JsonResponse;

class ClientController extends BaseApiController
{
    public function get(): JsonResponse
    {
        return $this->respondSuccessNoReport([
            'clients' => ClientsResource::collection(
                WholesaleClient::query()
                    ->with(['city:id,name'])
                    ->get(),
            )
        ]);
    }
}
