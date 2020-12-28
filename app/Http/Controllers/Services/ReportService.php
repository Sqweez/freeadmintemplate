<?php


namespace App\Http\Controllers\Services;


use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;

class ReportService {
    public static function getReports($start, $finish) {
        return ReportsResource::collection(
            Sale::report()->reportDate([$start, $finish])->get()
        );
    }
}
