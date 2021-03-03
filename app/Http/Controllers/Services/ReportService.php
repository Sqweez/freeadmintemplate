<?php


namespace App\Http\Controllers\Services;


use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use App\v2\Models\Supplier;

class ReportService {
    public static function getReports($start, $finish, $user_id, $is_supplier = false) {
        $saleQuery = Sale::query();
        $sales = null;
        if (!$is_supplier) {
            $saleQuery = $saleQuery->report()->reportDate([$start, $finish]);
            if ($user_id) {
                $saleQuery->where('user_id', $user_id);
            }
            $sales = $saleQuery->get();
        } else {
            $_supplier = Supplier::where('user_id', $user_id)->first();
            $supplierProducts = $_supplier->products->pluck('id')->toArray();
            $saleQuery = $saleQuery->reportDate([$start, $finish])->reportSupplier($supplierProducts);
            $supplierProducts = collect($supplierProducts);
            $sales = $saleQuery->get()->map(function($sale) use ($supplierProducts) {
                $products = $sale['products'];
                unset($sale['products']);
                $products = collect($products)->filter(function ($product) use ($supplierProducts) {
                    return $supplierProducts->contains($product['product']['product_id']);
                });
                $sale['products'] = $products;
                return $sale;

            });
        }

        return ReportsResource::collection(
            $sales
        );
    }
}
