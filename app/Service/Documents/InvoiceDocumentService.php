<?php

namespace App\Service\Documents;

use App\Document;
use App\v2\Models\WholesaleClient;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Storage;

class InvoiceDocumentService extends BaseDocumentService
{
    const PRODUCT_ROW = 25;

    public function getTemplatePath(): string
    {
        return storage_path('app/document_templates/invoice_payment_template.xlsx');
    }

    public function getDocumentType(): int
    {
        return Document::DOCUMENT_INVOICE_PAYMENT;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception|Exception
     */
    public function create(): string
    {
        \Log::info('CREATING INVOICE');
        $template = $this->loadTemplateFile();
        $this->fillExcelSheet($template);
        $excelWriter = new Xlsx($template);
        $fileName = $this->getFileName();
        $shortPath = 'opt/invoices/' . $fileName;
        $fullPath = storage_path('app/public/' . $shortPath);
        $directory = dirname($fullPath);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        $excelWriter->save($fullPath);
        return $shortPath;
    }

    private function getDocumentTitle(): string
    {
        return 'Счет № ' . $this->getDocumentNumber() . ' от ' . now()->format('d.m.Y');
    }

    private function getCustomerFullRequisites(): string
    {
        /* @var WholesaleClient $client */
        $client = $this->entity->client;
        return sprintf("%s %s", $client->iin, $client->full_name);
    }

    /**
     * @throws Exception
     */
    private function fillExcelSheet(Spreadsheet $spreadsheet): void
    {
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet->setCellValue('B15', $this->getDocumentTitle());
        $activeSheet->setCellValue('F21', $this->getCustomerFullRequisites());
        $activeSheet->setCellValue('B10',
            sprintf('ИНН %s', $this->legalEntityResolver->getIIN())
        );
        $activeSheet->setCellValue('B11',
            sprintf('%s', $this->legalEntityResolver->getName())
        );
        $activeSheet->setCellValue('F19',
            sprintf('ИИН %s %s, %s', $this->legalEntityResolver->getIIN(), $this->legalEntityResolver->getName(), $this->legalEntityResolver->getAddress())
        );

        $activeSheet->setCellValue('B7', $this->legalEntityResolver->getBankAccount()->title);
        $activeSheet->setCellValue('W7', $this->legalEntityResolver->getBankAccount()->BIK);
        $activeSheet->getCell('W8')
            ->setValueExplicit(
                $this->legalEntityResolver->getBankAccount()->IIK,
                DataType::TYPE_STRING2
            );

        $products = $this->productsResolver->resolve();
        foreach ($products['products'] as $key => $item) {
            $currentIndex = $key + self::PRODUCT_ROW;
            try {
                if ($key > 0) {
                    $activeSheet->insertNewRowBefore($currentIndex, 1);
                    $activeSheet->duplicateStyle($activeSheet->getStyle('B25:C25'), 'B' . ($currentIndex) . ':C' . ($currentIndex));
                    $activeSheet->duplicateStyle($activeSheet->getStyle('D25:T25'), 'D' . ($currentIndex) . ':T' . ($currentIndex));
                    $activeSheet->duplicateStyle($activeSheet->getStyle('U25:W25'), 'U' . ($currentIndex) . ':W' . ($currentIndex));
                    $activeSheet->duplicateStyle($activeSheet->getStyle('Z25:AC25'), 'Z' . ($currentIndex) . ':AC' . ($currentIndex));
                    $activeSheet->duplicateStyle($activeSheet->getStyle('AD25:AG25'), 'AD' . ($currentIndex) . ':AG' . ($currentIndex));
                }
            } catch (Exception $e) {
            }
            try {
                $activeSheet->mergeCells("B" . $currentIndex . ":C" . $currentIndex);
                $activeSheet->mergeCells("D" . $currentIndex . ":T" . $currentIndex);
                $activeSheet->mergeCells("U" . $currentIndex . ":W" . $currentIndex);
                $activeSheet->mergeCells("X" . $currentIndex . ":Y" . $currentIndex);
                $activeSheet->mergeCells("Z" . $currentIndex . ":AC" . $currentIndex);
                $activeSheet->mergeCells("AD" . $currentIndex . ":AG" . $currentIndex);
            } catch (\Exception $e) {
            }

            $activeSheet->setCellValue('B' . ($currentIndex), $key + 1);
            $activeSheet->setCellValue('D' . ($currentIndex), $item['name']);
            $activeSheet->setCellValue('X' . ($currentIndex), "шт.");
            $activeSheet->setCellValue('U' . ($currentIndex), $item['count']);
            $activeSheet->setCellValue('Z' . ($currentIndex), $item['price_per_product']);
            $activeSheet->setCellValue('AD' . ($currentIndex), $item['total_price']);

            $activeSheet->getRowDimension($currentIndex)->setRowHeight(-1);
            $activeSheet->getStyle('C' . $currentIndex)->getAlignment()->setWrapText(true);
        }

        $activeSheet->setCellValue('AG' . (self::PRODUCT_ROW + count($products['products'])), 'Итого: ' . $products['totalPrice']);
        $activeSheet->setCellValue('AG' . (self::PRODUCT_ROW + 1 + count($products['products'])), 'Без налога (НДС): ' . $products['totalPrice']);
        $activeSheet->setCellValue('AG' . (self::PRODUCT_ROW + 2 + count($products['products'])), 'Всего к оплате: ' . $products['totalPrice']);
        $activeSheet->setCellValue('B' . (28 + count($products['products'])), 'Всего наименований ' . $products['totalCount'] . ', на сумму ' . $products['totalPriceText'] . '.');
    }
}
