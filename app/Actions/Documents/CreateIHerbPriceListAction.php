<?php

namespace App\Actions\Documents;

use App\Http\Controllers\Services\ExcelService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CreateIHerbPriceListAction {

    private ExcelService $excelService;

    public function __construct() {
        $this->excelService = new ExcelService();
    }

    public function handle(array $products): JsonResponse {
        $excelFile = $this->excelService->loadFile('iherb_price_template');
        $excelSheet = $excelFile->getActiveSheet();
        $documentName = 'Прайс-лист IHERB от ' . now_format();
        $INITIAL_ROW = 4;
        $mappedCart = collect($products)->map(function ($item, $key) {
            $currentIndex = $key + 4;
            return [
                'key' => $key + 1,
                'name' => $item['excel_name'],
                'quantity_name' => 'шт.',
                'quantity' => $item['total_quantity'],
                'client_quantity' => 0,
                'price' => intval($item['product_price_rub']),
                'formula' => "=PRODUCT(E$currentIndex * F$currentIndex)"
            ];
        })->toArray();

        $excelSheet->fromArray($mappedCart, null, 'A4', true);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];


        $excelSheet->getStyle('A4:G' . (count($mappedCart) + $INITIAL_ROW - 1))
            ->applyFromArray($styleArray);

        foreach ([] as $key => $item) {
            $currentIndex = $key + $INITIAL_ROW;
            try {
                //$excelSheet->insertNewRowBefore($currentIndex, 1);
            } catch (Exception $e) {
                //
            }

            try {
                $excelSheet->mergeCells("B" . $currentIndex . ":N" . $currentIndex);
                $excelSheet->mergeCells("O" . $currentIndex . ":Q" . $currentIndex);
                $excelSheet->mergeCells("R" . $currentIndex . ":V" . $currentIndex);
                $excelSheet->mergeCells("W" . $currentIndex . ":Z" . $currentIndex);
                $excelSheet->mergeCells("AA" . $currentIndex . ":AF" . $currentIndex);
                $excelSheet->mergeCells("AG" . $currentIndex . ":AL" . $currentIndex);
                $excelSheet->getStyle('A' . $currentIndex . ':AL' . $currentIndex)
                    ->getFill()
                    ->setFillType(Fill::FILL_NONE);
            } catch (\Exception $e) {
                //
            }


            #$excelSheet->setCellValue('A' . ($currentIndex), $key + 1);
            #$excelSheet->setCellValue('B' . ($currentIndex), $item['excel_name']);
            #$excelSheet->setCellValue('O' . ($currentIndex), 'шт.');
            #$excelSheet->setCellValue('R' . ($currentIndex), $item['total_quantity']);
            // Ячейка клиента
            #$excelSheet->setCellValue('W' . ($currentIndex), 0);
            /*$validation = $excelSheet->getCell('W' . $currentIndex)->getDataValidation();
            $validation->setType( DataValidation::TYPE_WHOLE );
            $validation->setErrorStyle( DataValidation::STYLE_STOP );
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Ошибка ввода');
            $validation->setError('Недопустимое значения');
            $validation->setPromptTitle('Допустимый ввод');
            $validation->setPrompt('Допускаются числа от 0 до ' . $item['total_quantity']);
            $validation->setFormula1(0);
            $validation->setFormula2($item['total_quantity']);*/
            #$excelSheet->setCellValue('AA' . ($currentIndex), intval($item['final_price']));

            #$formula = "=PRODUCT(AA$currentIndex * W$currentIndex)";
            //$internalFormula = Calculation::getInstance()->_translateFormulaToEnglish($formula);
            #$excelSheet->setCellValue('AG' . ($currentIndex), $formula);
        }

        $TOTAL_INDEX = $INITIAL_ROW + count($mappedCart);
        $LAST_INDEX = $TOTAL_INDEX - 1;
        $excelSheet->setCellValue('D' . $TOTAL_INDEX, 'Итого');
        $formula = "=SUM(E$INITIAL_ROW:E$LAST_INDEX)";
        $excelSheet->setCellValue('E' . $TOTAL_INDEX, $formula);
        $formula = "=SUM(G$INITIAL_ROW:G$LAST_INDEX)";
        $excelSheet->setCellValue('G' . $TOTAL_INDEX, $formula);

        $excelSheet->getStyle("D$TOTAL_INDEX:G$TOTAL_INDEX")
            ->applyFromArray($styleArray);

        foreach (range('A', 'G') as $letter) {
            $excelSheet->getColumnDimension($letter)->setAutoSize(true);
        }

        $excelWriter = new Xlsx($excelFile);

        $fileName =  'ПРАЙС-IHERB' . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $path = 'storage/excel/iherb/';
        $fullPath = $path . $fileName;
        \File::ensureDirectoryExists($path);
        $excelWriter->save($fullPath);
        return response()->json([
            'path' => $fullPath
        ]);
    }
}
