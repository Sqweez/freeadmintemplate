<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Sale;

class CheckController extends Controller
{
    public function index(Sale $sale) {
        $reportResource = new ReportResource($sale);
        return view('check', [
            'report' => $reportResource->jsonSerialize()
        ]);
    }
}
