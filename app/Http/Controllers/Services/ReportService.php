<?php


namespace App\Http\Controllers\Services;


use App\DTO\Reports\ReportOptionsDTO;
use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

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
            ->when($reportOptionsDTO->currentUser->isFranchise(), function ($query) use ($reportOptionsDTO) {
                return $query->whereIn('store_id', $reportOptionsDTO->currentUser->storesInSameCity()->pluck('id'));
            })
            ->when($reportOptionsDTO->promocode_id, function ($query) use ($reportOptionsDTO) {
                return $query->where('promocode_id', $reportOptionsDTO->promocode_id);
            })
            ->when($reportOptionsDTO->manufacturer_id, function ($query) use ($reportOptionsDTO) {
                return $query->whereHas('products.product.product', function ($subQuery) use ($reportOptionsDTO) {
                    return $subQuery->where('manufacturer_id', $reportOptionsDTO->manufacturer_id);
                });
            })
            ->with('products')
            ->report()
            ->reportDate([$reportOptionsDTO->start, $reportOptionsDTO->finish])
        ;

        \Log::info($sales->toSql());

        return ReportsResource::collection($sales->get());
    }

    public function getReportsTotal($startDate, $endDate)
    {
        $saleProductsSubQuery = DB::table('sale_products')
            ->select(
                'sale_id',
                DB::raw('SUM(product_price - (discount / 100 * product_price)) as total_product_price'),
                DB::raw('SUM(purchase_price) as total_purchase_price'),
                DB::raw('SUM(CASE WHEN discount = 100 THEN 0 ELSE (product_price - (discount / 100 * product_price)) - purchase_price END) as products_margin')
            )->groupBy('sale_id');

        $results = DB::table('sales')
            ->selectRaw("
                    SUM(sps.total_product_price) as total_product_price,
                    SUM(sps.total_purchase_price) as total_purchase_price,
                    SUM(sps.products_margin) as products_margin,
                    COALESCE(SUM(purchased_certs.amount), 0) as total_certificates_sold,
                    COALESCE(SUM(used_certificates.amount), 0) as total_certificates_paid,
                    SUM(CASE
                        WHEN sales.kaspi_red THEN
                            (sps.total_product_price - sales.balance + COALESCE(purchased_certs.amount, 0) - sales.promocode_fixed_amount) * 0.11
                        ELSE 0
                    END) as kaspi_red_comission,
                    SUM(sales.balance) as total_balance,
                    (
                        SUM(sps.total_product_price)
                        - SUM(sales.balance)
                        + COALESCE(SUM(purchased_certs.amount), 0)
                        - SUM(sales.promocode_fixed_amount)
                    ) as total_final_price
                ")
            ->selectRaw('
                    SUM(sps.products_margin) +
                    SUM(GREATEST(0, COALESCE(used_certificates.amount, 0) - (sps.total_product_price - sales.balance + COALESCE(purchased_certs.amount, 0) - sales.promocode_fixed_amount))
                    ) as total_margin'
            )
            // Добавить сертификат - закуп
            ->leftJoinSub($saleProductsSubQuery, 'sps', function ($join) {
                $join->on('sales.id', '=', 'sps.sale_id');
            })
            ->selectRaw('COUNT(*) as sales_count')
            ->leftJoin('certificates as purchased_certs', function($join) {
                $join->on('sales.id', '=', 'purchased_certs.sale_id')
                    ->where(function($query) {
                        $query->whereNull('purchased_certs.used_sale_id')
                            ->orWhere('purchased_certs.used_sale_id', '=', 0);
                    });
            })
            ->leftJoin('certificates as used_certificates', 'sales.id', '=', 'used_certificates.used_sale_id')
            ->whereDate('sales.created_at', '>=', Carbon::parse($startDate)->startOfDay())
            ->whereDate('sales.created_at', '<=', Carbon::parse($endDate)->endOfDay())
            ->get();

        $results = (array) $results->first();

        return [
            'total_final_price' => (float)$results['total_final_price'],
            'total_margin' => (float)$results['total_margin'] - (float)$results['kaspi_red_comission'],
            'kaspi_red_comission' => (float)$results['kaspi_red_comission'],
            'sales_count' => (int)$results['sales_count'],
            'average_total' => (float)$results['total_final_price'] / (int)$results['sales_count']
        ];
    }

    public static function getClientReports($client_id): AnonymousResourceCollection {
        $saleQuery = Sale::query();
        $saleQuery = $saleQuery->report()->whereClientId($client_id)->orderByDesc('created_at');

        return ReportsResource::collection($saleQuery->get());
    }
}
