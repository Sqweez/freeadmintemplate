<?php

namespace App\Http\Controllers;

use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function index($sale, Request $request) {
        $report = $this->getReport($sale, $request)->getData(true);
        if ($report['payment_type'] === 2) {
            $report['final_price'] += $report['final_price'] * Sale::KASPI_RED_PERCENT;
        }
        return view('check', [
            'report' => (object) $report,
        ]);
    }

    private function getReport($sale, $request) {
        $reportResource = new ReportsResource(Sale::report()->whereKey($sale)->first());
        return response()->json($reportResource->toArray($request));
    }
}
