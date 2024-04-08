<?php

namespace App\Service\Documents;

use App\Document;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Storage;

class WaybillDocumentService extends BaseDocumentService
{

    const PRODUCT_ROW = 24;

    public function getTemplatePath(): string
    {
        return storage_path('app/document_templates/waybill_template.xls');
    }

    public function getDocumentType(): int
    {
        return Document::DOCUMENT_WAYBILL;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function create(): string
    {
        $template = $this->loadTemplateFile();
        $this->fillExcelSheet($template);
        $excelWriter = new Xlsx($template);
        $fileName = $this->getFileName();
        $shortPath = 'opt/waybills/' . $fileName;
        $fullPath = storage_path('app/public/' . $shortPath);
        $directory = dirname($fullPath);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        $excelWriter->save($fullPath);
        return $shortPath;
    }

    private function fillExcelSheet(Spreadsheet $spreadsheet): void
    {
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet->setCellValue('N9', $this->legalEntityResolver->getName());
        $activeSheet->setCellValue('A19', $this->legalEntityResolver->getName());
        $activeSheet->setCellValue('AQ9', $this->legalEntityResolver->getIIN());
        $activeSheet->setCellValue('AP13', $this->getDocumentNumber());
        $activeSheet->setCellValue('AT13', $this->getDocumentDate());
        $activeSheet->setCellValue('AF21', 'Цена за единицу, в ' . $this->getCurrencyCode());
        $activeSheet->setCellValue('AL21', 'Сумма с НДС, в ' . $this->getCurrencyCode());
        $activeSheet->setCellValue('AR21', 'Сумма НДС, в ' . $this->getCurrencyCode());
        $products = $this->productsResolver->resolve();
        foreach ($products['products'] as $key => $item) {
            $currentIndex = $key + self::PRODUCT_ROW;
            try {
                $activeSheet->insertNewRowBefore($currentIndex, 1);
            } catch (Exception $e) {
            }
            try {
                $activeSheet->mergeCells("A" . $currentIndex . ":B" . $currentIndex);
                $activeSheet->mergeCells("C" . $currentIndex . ":N" . $currentIndex);
                $activeSheet->mergeCells("O" . $currentIndex . ":S" . $currentIndex);
                $activeSheet->mergeCells("T" . $currentIndex . ":V" . $currentIndex);
                $activeSheet->mergeCells("W" . $currentIndex . ":AA" . $currentIndex);
                $activeSheet->mergeCells("AB" . $currentIndex . ":AE" . $currentIndex);
                $activeSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $activeSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $activeSheet->mergeCells("AL" . $currentIndex . ":AQ" . $currentIndex);
                $activeSheet->mergeCells("AR" . $currentIndex . ":AW" . $currentIndex);
            } catch (\Exception $e) {
            }

            $activeSheet->setCellValue('A' . ($currentIndex), $key + 1);
            $activeSheet->setCellValue('C' . ($currentIndex), $item['name']);
            $activeSheet->setCellValue('T' . ($currentIndex), "шт.");
            $activeSheet->setCellValue('W' . ($currentIndex), $item['count']);
            $activeSheet->setCellValue('AB' . ($currentIndex), $item['count']);
            $activeSheet->setCellValue('AF' . ($currentIndex), $item['price_per_product']);
            $activeSheet->setCellValue('AL' . ($currentIndex),$item['total_price']);

            $activeSheet->getRowDimension($currentIndex)->setRowHeight(-1);
            $activeSheet->getStyle('C' . $currentIndex)->getAlignment()->setWrapText(true);
        }

        $footerRow = self::PRODUCT_ROW + count($products['products']);
        $activeSheet->setCellValue('W' . ($footerRow), $products['totalCount']);
        $activeSheet->setCellValue('AB' . ($footerRow), $products['totalCount']);
        $activeSheet->setCellValue('AL' . ($footerRow), $products['totalPrice']);
        $footerRow += 2;
        $activeSheet->setCellValue('N' . $footerRow, $products['totalCountText']);
        $activeSheet->setCellValue('AE' . $footerRow, $products['totalPriceText']);
        $activeSheet->setCellValue('W' . $footerRow, 'на сумму (прописью), в ' . $this->getCurrencyCode());
    }
}
