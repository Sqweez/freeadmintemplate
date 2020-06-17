<?php


namespace App\Http\Controllers\Services;


use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService {

    public function createExcel() {
        $fileName = 'products_15_06.json';
        $jsonFile = Storage::get('public/json/' . $fileName);
        $data = json_decode($jsonFile, true);
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setCellValue('A1', 'id');
        $sheet->setCellValue('B1', 'Название');
        $sheet->setCellValue('C1', 'Категория');
        $sheet->setCellValue('D1', 'Атрибуты');
        $sheet->setCellValue('E1', 'Производитель');
        $sheet->setCellValue('F1', 'Стоимость');
        $sheet->setCellValue('G1', 'Количество');


        foreach ($data as $key => $datum) {
            $sheet->setCellValue('A' . ($key + 2), $datum['id']);
            $sheet->setCellValue('B' . ($key + 2), $datum['product_name']);
            $sheet->setCellValue('C' . ($key + 2), $datum['categories']);
            //$sheet->setCellValue('D' . ($key + 2), 'Атрибуты');
            $sheet->setCellValue('E' . ($key + 2), $datum['manufacturer']);
            $sheet->setCellValue('F' . ($key + 2),  $datum['product_price']);

            $attributes = '';

            foreach ($datum['attributes'] as $attribute) {
                $attributes .= $attribute['attribute'] . ":" . $attribute['attribute_value'] . ' | ';
            }
            $sheet->setCellValue('D' . ($key + 2), $attributes);
        }

        $writer = new Xlsx($spreadSheet);

        $writer->save('products.xlsx');

        return $data;
    }

}
