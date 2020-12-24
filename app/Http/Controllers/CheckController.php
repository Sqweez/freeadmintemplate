<?php

namespace App\Http\Controllers;

use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function index($sale, Request $request) {
        return view('check', [
            'report' => $this->getReport($sale, $request)->getData()
        ]);
    }

    private function getReport($sale, $request) {
        $reportResource = new ReportsResource(Sale::report()->whereKey($sale)->first());
        return response()->json($reportResource->toArray($request));
    }
}
