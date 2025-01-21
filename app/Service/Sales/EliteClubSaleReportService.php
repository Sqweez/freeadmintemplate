<?php

namespace App\Service\Sales;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EliteClubSaleReportService
{
    public function getEliteExcelReport(Builder $builder): string
    {
        $sales = $builder->get();
        $sales = $sales
            ->groupBy('client_id')
            ->map(function ($sales) {
                $products = collect($sales)
                    ->flatMap(function ($sale) { return $sale['products']; })
                    ->groupBy('product_id')
                    ->map(function ($products) {
                        $product = $products->first();
                        return [
                            'name' => $product['product']['sku_name'],
                            'quantity' => $products->count(),
                            'price' => $products->reduce(function ($a, $c) {
                                return $a + ceil($c->final_price);
                            }, 0),
                        ];
                    })
                    ->values();
                $sale = collect($sales)->first();
                $totalPrice = $products->sum('price');
                $avgPrice = $totalPrice / $sales->count();
                return [
                    'client' => $sale['client']['client_name'],
                    'products' => $products,
                    'total_price' => $totalPrice,
                    'avg_price' => $avgPrice,
                    'city' => $sale['store']['city_name']['name']
                ];
            })
            ->values();

        return $this->generateExcelFile($sales);
    }

    private function generateExcelFile(Collection $sales): string
    {
        $spreadsheet = new Spreadsheet();
        $salesByCity = $sales->groupBy('city');
        foreach ($salesByCity as $city => $items) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($city);
            $this->writeSheet($sheet, $items);
        }
        $spreadsheet->removeSheetByIndex(0);
        $spreadsheet->setActiveSheetIndex(0);
        $fileName = sprintf("%s_%s_elite_report.xlsx", now()->format('Y-m-d_H-i-s'), uniqid());
        $path = "excel/reports";
        if (!\Storage::disk('public')->exists($path)) {
            \Storage::disk('public')->makeDirectory($path);
        }
        $filePath = Storage::path("public/{$path}/{$fileName}");
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return "storage/{$path}/{$fileName}";
    }

    private function writeSheet(Worksheet $sheet, Collection $collection): void
    {
        $sheet->setCellValue('A1', 'Клиент');
        $sheet->setCellValue('B1', 'Товары');
        $sheet->setCellValue('C1', 'Средний чек');
        $sheet->setCellValue('D1', 'Общая сумма');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFE0B2');

        $row = 2;
        foreach ($collection as $item) {
            $numberedList = collect($item['products'])->map(function ($product, $index) {
                return ($index + 1) . '. ' . $product['name'] . ' - ' . $product['quantity'] . ' шт.';
            })->implode("\n");
            $sheet->setCellValue("A{$row}", $item['client']);
            $sheet->setCellValue("B{$row}", $numberedList);
            $sheet->setCellValue("C{$row}", $item['avg_price']);
            $sheet->setCellValue("D{$row}", $item['total_price']);
            $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
            $row++;
        }

        $sheet->getStyle('D2:D' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00₸');

        $sheet->getStyle('C2:D' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00₸');


        // Выравнивание и авторазмер для колонок
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setAutoFilter("A1:D" . ($row - 1));
    }
}
