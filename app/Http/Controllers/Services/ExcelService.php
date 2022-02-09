<?php


namespace App\Http\Controllers\Services;


use App\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelService {

    public function createExcel($fileName = 'products.json') {
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
            $sheet->setCellValue('F' . ($key + 2), $datum['product_price']);
            $sheet->setCellValue('G' . ($key + 2), $datum['quantity']);

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

    public function createRevisionExcel($jsonData = []) {
        $spreadSheet = new Spreadsheet();

        $sheet = $spreadSheet->getActiveSheet();

        $spreadSheet->getDefaultStyle()->getFont()->setName('Arial');

        $sheet->setCellValue('A1', 'Артикул');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->setCellValue('C1', 'Категория');
        $sheet->setCellValue('D1', 'Атрибуты');
        $sheet->setCellValue('E1', 'Производитель');
        $sheet->setCellValue('F1', 'Стоимость');
        $sheet->setCellValue('G1', 'Количество');
        $sheet->setCellValue('H1', 'Факт количество');

        $key = 0;

        foreach ($jsonData as $key => $datum) {
            $sheet->setCellValue('A' . ($key + 2), $datum['id']);
            $sheet->setCellValue('B' . ($key + 2), $datum['product_name']);
            $sheet->setCellValue('C' . ($key + 2), $datum['categories']);
            $sheet->setCellValue('D' . ($key + 2), 'Атрибуты');
            $sheet->setCellValue('E' . ($key + 2), $datum['manufacturer']);
            $sheet->setCellValue('F' . ($key + 2), $datum['product_price']);
            $sheet->setCellValue('G' . ($key + 2), $datum['quantity']);

            $attributes = '';
            foreach ($datum['attributes'] as $attribute) {
                $attributes .= $attribute['attribute_value'] . ' ';
            }


            $sheet->setCellValue('D' . ($key + 2), $attributes);
        }

        foreach (range("B", "H") as $coordinate) {
            $sheet->getStyle('D2:D' . $key)->getAlignment()->setWrapText(true);

            if ($coordinate !== "D") {
                $sheet->getColumnDimension($coordinate)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadSheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();

        $fileName = Carbon::now()->toDateString() . '_' . Str::random(10) . '_products.xlsx';
        $file = 'public/excel/revision/templates/' . $fileName;
        Storage::put($file, $content);
        return url('/') . Storage::url($file);
    }

    public function parseRevisionExcel($filename) {
        $file = explode('/', $filename);
        $file = $file[1] . '/' . $file[2];
        $excelFile = $this->loadFile($file, '');
        $sheet = $excelFile->getActiveSheet();
        $rows = $sheet->getRowIterator();
        $products = [];
        foreach ($rows as $key => $row) {
            if ($key > 1) {
                $products[] = [
                    'id' => $sheet->getCell('A' . $key)->getValue(),
                    'stock_quantity' => $sheet->getCell('G' . $key)->getValue(),
                    'fact_quantity' => $sheet->getCell('H' . $key)->getValue() ?? 0,
                ];
            }
        }

        return $products;
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
                    array_push($products, ['id' => $sheet->getCell('A' . $key)->getValue(), 'name' => $sheet->getCell('B' . $key)->getValue(), 'count' => $sheet->getCell('G' . $key)->getValue(),]);
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
