<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\api\BaseApiController;
use App\Service\Sales\SaleService;
use Illuminate\Http\Request;

class SaleController extends BaseApiController
{
    public function store(Request $request, SaleService $saleService)
    {
    }
}
