<?php

namespace App\Service\Ecommerce;

use App\v2\Models\KaspiEntity;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HalykProductExcelGenerator implements ProductXMLGenerator
{

    /**
     * @throws Exception
     */
    public function generate(array $products, KaspiEntity $kaspiEntity): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet = $this->createColumns($sheet);
        $sheet = $this->fillProducts($products, $sheet);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Применяем стили к диапазону данных
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray($styleArray);

        foreach (range('A', $highestColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    private function fillProducts(array $products, $sheet)
    {
        $contentArray = [];
        foreach ($products as $product) {
            $quantity = collect($product['quantities'])->where('store_id', __hardcoded(1))->first();
            $quantity = $quantity['quantity'] ?? '';
            $contentArray[] = [
                $product['sku'],
                $product['product_name'],
                '',
                $product['price'],
                24,
                $quantity
            ];
        }
        $sheet->fromArray($contentArray, null, 'A2');
        return $sheet;
    }

    private function createColumns(Worksheet $sheet): Worksheet
    {
        $sheet->setCellValue('A1', 'SKU');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'LoanPeriod');
        $sheet->setCellValue('F1', 'iron-addicts kz_pp1');
        return $sheet;
    }

    public function getBaseName(): string
    {
        return 'halyk\excel\halyk_products_';
    }
}
