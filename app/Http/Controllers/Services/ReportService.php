<?php


namespace App\Http\Controllers\Services;


use App\DTO\Reports\ReportOptionsDTO;
use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService {
    /**
     */
    public function getReportsQuery(ReportOptionsDTO $reportOptionsDTO): Builder {
        return Sale::query()
            ->when($reportOptionsDTO->user, function ($query) use ($reportOptionsDTO) {
                return $query->where('store_id', $reportOptionsDTO->user->store_id);
            })
            ->when($reportOptionsDTO->payment_type, function ($query) use ($reportOptionsDTO) {
                return $query->where('payment_type', $reportOptionsDTO->payment_type);
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
            ->reportDate([$reportOptionsDTO->start, $reportOptionsDTO->finish]);
    }

    /**
     * @throws Exception
     */
    public function getExcelProductReport(Builder $builder): string
    {
        $sales = $builder->get();
        $skuSales = $sales
            ->flatMap(function ($sale) {
                return $sale['products'];
            })
            ->groupBy('product_id')
            ->map(function ($products, $productId) {
                $realProduct = $products->first()['product'];
                $skuProduct = $realProduct['product'];
                $fullProductSkuName = sprintf(
                    "%s %s %s",
                    $realProduct['product_name'],
                    $realProduct['manufacturer']['manufacturer_name'],
                    collect($realProduct['attributes'])
                        ->mergeRecursive($skuProduct['attributes'])
                        ->pluck('attribute_value')
                        ->join(' ')
                );
                return [
                    'product_id' => $productId,
                    'product_name' => $fullProductSkuName,
                    '_product_name' => sprintf(
                        "%s %s %s",
                        $realProduct['product_name'],
                        $realProduct['manufacturer']['manufacturer_name'],
                        collect($skuProduct['attributes'])
                            ->pluck('attribute_value')
                            ->join(' ')
                    ),
                    'total_quantity' => $products->count(),
                    'price' => $products->reduce(function ($a, $c) {
                        return $a + ceil($c->final_price);
                    }, 0),
                    '_product_id' => $skuProduct['id']
                ];
            })
            ->values();

        $productSales = $skuSales
            ->groupBy('_product_id')
            ->map(function ($products, $productId) {
                $product = $products->first();
                return [
                    'product_id' => $productId,
                    'product_name' => $product['_product_name'],
                    'total_quantity' => $products->sum('total_quantity'),
                    'price' => $products->sum('price'),
                ];
            });

        return $this->generateExcelFile($skuSales, $productSales);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function generateExcelFile(Collection $skuCollection, Collection $productCollection): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Товарные предложения');
        $this->writeSheet($sheet, $skuCollection);
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Товары');
        $this->writeSheet($sheet2, $productCollection);
        $spreadsheet->setActiveSheetIndex(0);
        // Сохраняем Excel файл в локальное хранилище
        $fileName = sprintf("%s_%s_report.xlsx", now()->format('Y-m-d_H-i-s'), uniqid());
        $path = "excel/reports";
        if (!\Storage::disk('public')->exists($path)) {
            \Storage::disk('public')->makeDirectory($path);
        }
        $filePath = Storage::path("public/{$path}/{$fileName}");
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return "storage/{$path}/{$fileName}";
    }

    private function writeSheet($sheet, Collection $collection)
    {
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->setCellValue('C1', 'Количество');
        $sheet->setCellValue('D1', 'Общая стоимость');


        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFE0B2');

        // Добавляем данные
        $row = 2;
        foreach ($collection as $item) {
            $sheet->setCellValue("A{$row}", $item['product_id']);
            $sheet->setCellValue("B{$row}", $item['product_name']);
            $sheet->setCellValue("C{$row}", $item['total_quantity']);
            $sheet->setCellValue("D{$row}", $item['price']);
            $row++;
        }

        $sheet->getStyle('D2:D' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00₸');

        // Выравнивание и авторазмер для колонок
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setAutoFilter("A1:D" . ($row - 1));
    }

    public function toResource(Builder $query): AnonymousResourceCollection
    {
        return ReportsResource::collection($query->get());
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
