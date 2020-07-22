<?php


namespace App\Http\Controllers\Services;


use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelService {

    public function createExcel() {
        $fileName = 'products.json';
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
            $sheet->setCellValue('D' . ($key + 2), 'Атрибуты');
            $sheet->setCellValue('E' . ($key + 2), $datum['manufacturer']);
            $sheet->setCellValue('F' . ($key + 2),  $datum['product_price']);
            $sheet->setCellValue('G' . ($key + 2),  $datum['quantity']);

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

    public function parseExcel($filename) {
        $excelFile = $this->loadFile($filename);
        $sheet = $excelFile->getActiveSheet();
        $rows = $sheet->getRowIterator();
        $products = [];
        foreach ($rows as $key => $item) {
            if ($key > 1) {
                $count = intval($sheet->getCell('G' . $key)->getValue());
                if ($count > 0) {
                    array_push($products, [
                        'id' => $sheet->getCell('A' . $key)->getValue(),
                        'name' => $sheet->getCell('B' . $key)->getValue(),
                        'count' => $sheet->getCell('G' . $key)->getValue(),
                    ]);
                }

            }
        }
        $jsonData = json_encode($products, JSON_UNESCAPED_UNICODE);
        $fileName = 'public/json/' . $filename . '.json';
        Storage::put($fileName, $jsonData);
        return $products;
    }

    public function loadFile($filename, $ext = 'xlsx') {
        $path = 'app/public/excel/' . $filename . '.' . $ext;
        $file = storage_path($path);
        return IOFactory::load($file);
    }

}
