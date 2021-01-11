<?php


namespace App\Http\Controllers\Services;


use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;

class ReportService {
    public static function getReports($start, $finish, $user_id) {
        $saleQuery = Sale::query()->report()->reportDate([$start, $finish]);
        if ($user_id) {
            $saleQuery->where('user_id', $user_id);
        }
        return ReportsResource::collection(
            $saleQuery->get()
        );
    }
}
