<?php


namespace App\Http\Controllers\Services;


use App\DTO\Reports\ReportOptionsDTO;
use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportService {
    /**
     */
    public static function getReports(ReportOptionsDTO $reportOptionsDTO): AnonymousResourceCollection {
        $sales = Sale::query()
            ->when($reportOptionsDTO->user, function ($query) use ($reportOptionsDTO) {
                return $query->where('store_id', $reportOptionsDTO->user->store_id);
            })
            ->when($reportOptionsDTO->store_id, function ($query) use ($reportOptionsDTO) {
                return $query->where('store_id', $reportOptionsDTO->store_id);
            })
            ->when(!$reportOptionsDTO->currentUser->is_super_user, function ($query) {
                return $query->where('is_confirmed', true);
            })
            ->when(!$reportOptionsDTO->currentUser->isFranchise(), function ($query) use ($reportOptionsDTO) {
                return $query->whereIn('store_id', $reportOptionsDTO->currentUser->storesInSameCity->pluck('id'));
            })
            ->when($reportOptionsDTO->promocode_id, function ($query) use ($reportOptionsDTO) {
                return $query->where('promocode_id', $reportOptionsDTO->promocode_id);
            })
            ->when($reportOptionsDTO->manufacturer_id, function ($query) use ($reportOptionsDTO) {
                return $query->whereHas('products.product.product', function ($subQuery) use ($reportOptionsDTO) {
                    return $subQuery->where('manufacturer_id', $reportOptionsDTO->manufacturer_id);
                });
            })
            ->report()
            ->reportDate([$reportOptionsDTO->start, $reportOptionsDTO->finish])
            ->get();

        return ReportsResource::collection($sales);
    }

    public static function getClientReports($client_id): AnonymousResourceCollection {
        $saleQuery = Sale::query();
        $saleQuery = $saleQuery->report()->whereClientId($client_id)->orderByDesc('created_at');

        return ReportsResource::collection($saleQuery->get());
    }
}
