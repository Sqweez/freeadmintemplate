<?php

namespace App\Http\Controllers\api\v3;

use App\Http\Controllers\api\BaseApiController;
use App\v2\Models\KaspiEntity;
use Illuminate\Http\JsonResponse;

class KaspiController extends BaseApiController
{
    public function getEntities(): JsonResponse
    {
        return $this->respondSuccess([
            'kaspi_entities' => KaspiEntity::all(),
        ]);
    }
}
