<?php

namespace App\Http\Controllers\api;

use App\Arrival;
use App\Client;
use App\Document;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ExcelService;
use App\Http\Resources\ArrivalResource;
use App\Http\Resources\SingleTransferResource;
use App\Order;
use App\Store;
use App\Transfer;
use App\v2\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WaybillController extends Controller
{

    const ROW_PADDING = 5;
    const DEFAULT_CELL_WIDTH = 9.14;
    const DEFAULT_ROW_HEIGHT = 15;
    const MONTHS_RU = [
        'Января', 'Февраля',
        'Марта', 'Апреля',
        'Мая', 'Июня',
        'Июля', 'Августа',
        'Сентября', 'Октября',
        'Ноября', 'Декабря',
    ];

    public function getDocuments() {
        return Document::all();
    }

    public function getClientList(Request $request) {

    }

    public function transferWaybill(Request $request)
    {
        $transfer_id = $request->get('transfer') ?? -1;
        $arrival_id = $request->get('arrival') ?? -1;

        if ($transfer_id !== -1) {
            $transfer = new SingleTransferResource(Transfer::find($transfer_id));
            $transfer = $transfer->toArray($request);
            $cart = $transfer['products'];
            $parent_store = $transfer['parent_store'];
            $child_store = $transfer['child_store'];
        } else if ($arrival_id !== -1) {
            $parent_store = 1;
            $child_store = 1;
            $arrival = new ArrivalResource(Arrival::find($arrival_id));
            $arrival = $arrival->toArray($request);
            $cart = $arrival['products'];
        } else {
            $cart = $request->get('cart');
            $parent_store = $request->get('parent_store');
            $child_store = $request->get('child_store');
        }
        $fileType = $this->getFileType($request);
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('waybill_transfer_template', 'xls');
        $excelSheet = $excelTemplate->getActiveSheet();

        $INITIAL_PRODUCT_ROW = 24;
        $PRODUCT_COUNT = count($cart);
        $TOTAL_COST = $this->getTotalCost($cart);
        $TOTAL_COUNT = $this->getTotalCount($cart);

        $parent_city = Store::find($parent_store)->name;
        $child_city = Store::find($child_store)->name;

        $parent_store_name = 'IRON ADDICTS, ' . $parent_city;
        $child_store_name = 'IRON ADDICTS, ' . $child_city;

        $excelSheet->setCellValue('L18', $parent_store_name);
        if ($fileType !== 'Продажа') {
            $excelSheet->setCellValue('L19', $child_store_name);
        }

        foreach ($cart as $key => $item) {
            $item = gettype($item) === 'array' ? $item : $item->toArray($request);
            $currentIndex = $key + $INITIAL_PRODUCT_ROW;
            try {
                $excelSheet->insertNewRowBefore($currentIndex, 1);
            } catch (Exception $e) {
            }
            try {
                $excelSheet->mergeCells("A" . $currentIndex . ":B" . $currentIndex);
                $excelSheet->mergeCells("C" . $currentIndex . ":N" . $currentIndex);
                $excelSheet->mergeCells("O" . $currentIndex . ":S" . $currentIndex);
                $excelSheet->mergeCells("T" . $currentIndex . ":V" . $currentIndex);
                $excelSheet->mergeCells("W" . $currentIndex . ":AA" . $currentIndex);
                $excelSheet->mergeCells("AB" . $currentIndex . ":AE" . $currentIndex);
                $excelSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $excelSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $excelSheet->mergeCells("AL" . $currentIndex . ":AQ" . $currentIndex);
                $excelSheet->mergeCells("AR" . $currentIndex . ":AW" . $currentIndex);
            } catch (\Exception $e) {
            }

            $excelSheet->setCellValue('A' . ($currentIndex), $key + 1);
            $excelSheet->setCellValue('C' . ($currentIndex), $this->getProductName($item));
            $excelSheet->setCellValue('T' . ($currentIndex), "шт.");
            $excelSheet->setCellValue('W' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('AB' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('AF' . ($currentIndex), mb_convert_case($item['product_price'], MB_CASE_TITLE, 'UTF-8'));
            $excelSheet->setCellValue('AL' . ($currentIndex), mb_convert_case(intval($item['product_price']) * intval($item['count']), MB_CASE_TITLE, 'UTF-8'));
            $excelSheet->setCellValue('AR' . ($currentIndex), mb_convert_case(intval($item['product_price']) * intval($item['count']), MB_CASE_TITLE, 'UTF-8'));

            $excelTemplate->getActiveSheet()->getRowDimension($currentIndex)->setRowHeight(-1);
            $excelTemplate->getActiveSheet()->getStyle('C' . $currentIndex)->getAlignment()->setWrapText(true);
        }

        $excelSheet->setCellValue('AR' . ($INITIAL_PRODUCT_ROW + $PRODUCT_COUNT), $TOTAL_COST);
        $excelSheet->setCellValue('AE' . (26 + $PRODUCT_COUNT), $this->number2string($TOTAL_COST));
        $excelSheet->setCellValue('N' . (26 + $PRODUCT_COUNT), $this->number2string($TOTAL_COUNT));

        $excelWriter = new Xlsx($excelTemplate);

        $fileName =  $fileType . "_" . Carbon::today()->toDateString() . "_" . $parent_city . '-' . $child_city . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/waybills/' . $fileName;

        $excelWriter->save($fullPath);

        return response()->json([
            'path' => $fullPath
        ]);
    }

    public function getProductReport(Request $request) {
        $products = (new SaleController())->getReportProducts($request)->toArray();
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('product_report_template', 'xlsx');
        $excelSheet = $excelTemplate->getActiveSheet();
        $INITIAL_ROW = 2;
        foreach ($products as $key => $product) {
            $currentIndex = $key + $INITIAL_ROW;
            try {
                $excelSheet->insertNewRowBefore($currentIndex, 1);
            } catch (Exception $e) {
            }
            $excelSheet->setCellValue('A' . $currentIndex, $key + 1);
            $excelSheet->setCellValue('B' . $currentIndex, $product['product_name'] . ' ' . $product['attributes'] . ', ' . $product['manufacturer']);
            $excelSheet->setCellValue('C' . $currentIndex, $product['count']);
            $excelSheet->setCellValue('D' . $currentIndex, number_format(intval($product['total_purchase_price']), 2, ',', ' '));
            $excelSheet->setCellValue('E' . $currentIndex, number_format(intval($product['total_product_price']), 2, ',', ' '));
            $excelSheet->setCellValue('F' . $currentIndex, number_format(intval($product['margin']), 2, ',', ' '));
        }

        $excelWriter = new Xlsx($excelTemplate);
        $fileName =  'ВЫГРУЗКА_ТОВАРНЫХ_ОТЧЕТОВ' . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/batches/' . $fileName;
        $excelWriter->save($fullPath);

        return response()->json([
            'path' => $fullPath
        ]);
    }

    public function getPriceList(Request $request, $_cart = []) {
        $cart = count($_cart) === 0 ? $request->get('cart') : $_cart;
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('price_list_template', 'xlsx');
        $excelSheet = $excelTemplate->getActiveSheet();
        $INITIAL_PRODUCT_ROW = 8;
        $excelSheet->insertNewRowBefore(9, count($cart));
        foreach ($cart as $key => $item) {
            $currentIndex = $key + $INITIAL_PRODUCT_ROW;
            if ($key > 0) {
                try {
                    //$excelSheet->insertNewRowBefore($currentIndex, 1);
                } catch (\Exception $exception) {}
            }


            try {
                $excelSheet->setCellValue('A' . $currentIndex, $key + 1);
                $excelSheet->setCellValue('B' . $currentIndex, $this->getProductName($item));
                $excelSheet->setCellValue('C' . $currentIndex, $item['product_price']);
                $excelSheet->setCellValue('D' . $currentIndex, $this->calculateDiscount($item['product_price'], 0.18));
                $excelSheet->setCellValue('E' . $currentIndex, $this->calculateDiscount($item['product_price'], 0.20));
                $excelSheet->setCellValue('F' . $currentIndex, $this->calculateDiscount($item['product_price'], 0.22));
                $excelSheet->getStyle('G' . $currentIndex)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('92D050');
                $excelSheet->setCellValue('H' . $currentIndex, $item['quantity']);
            } catch (\Exception $exception) {}
        }

        $excelWriter = new Xlsx($excelTemplate);
        $fileName =  'ПРАЙС_ЛИСТ' . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/waybills/' . $fileName;
        $excelWriter->save($fullPath);

        return response()->json([
            'path' => $fullPath
        ]);
    }

    private function calculateDiscount($price, $discount) {
        return $price - ($price * $discount);
    }

    public function createWaybill(Request $request) {
        $documentType = Document::DOCUMENT_WAYBILL;
        $cart = $request->get('cart');
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('waybill_doc_template', 'xls');
        $excelSheet = $excelTemplate->getActiveSheet();
        $INITIAL_PRODUCT_ROW = 24;
        $PRODUCT_COUNT = count($cart);
        $TOTAL_COST = $this->getTotalCost($cart);
        $TOTAL_COUNT = $this->getTotalCount($cart);
        $documentNumber = $this->getDocumentNumber($documentType);
        $documentDate = now()->format('d.m.Y');
        $organization = $request->get('organization');
        $excelSheet->setCellValue('L19', $organization);
        $excelSheet->setCellValue('AP13', $documentNumber);
        $excelSheet->setCellValue('AT13', $documentDate);
        foreach ($cart as $key => $item) {
            $currentIndex = $key + $INITIAL_PRODUCT_ROW;
            try {
                $excelSheet->insertNewRowBefore($currentIndex, 1);
            } catch (Exception $e) {
            }
            try {
                $excelSheet->mergeCells("A" . $currentIndex . ":B" . $currentIndex);
                $excelSheet->mergeCells("C" . $currentIndex . ":N" . $currentIndex);
                $excelSheet->mergeCells("O" . $currentIndex . ":S" . $currentIndex);
                $excelSheet->mergeCells("T" . $currentIndex . ":V" . $currentIndex);
                $excelSheet->mergeCells("W" . $currentIndex . ":AA" . $currentIndex);
                $excelSheet->mergeCells("AB" . $currentIndex . ":AE" . $currentIndex);
                $excelSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $excelSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $excelSheet->mergeCells("AL" . $currentIndex . ":AQ" . $currentIndex);
                $excelSheet->mergeCells("AR" . $currentIndex . ":AW" . $currentIndex);
            } catch (\Exception $e) {
            }

            $excelSheet->setCellValue('A' . ($currentIndex), $key + 1);
            $excelSheet->setCellValue('C' . ($currentIndex), $this->getProductName($item));
            $excelSheet->setCellValue('T' . ($currentIndex), "шт.");
            $excelSheet->setCellValue('W' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('AB' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('AF' . ($currentIndex), number_format(intval($this->getProductCost($item)), 2, ',', ' '));
            $excelSheet->setCellValue('AL' . ($currentIndex), number_format(intval($this->getProductCost($item) * $item['count']), 2, ',', ' '));

            $excelTemplate->getActiveSheet()->getRowDimension($currentIndex)->setRowHeight(-1);
            $excelTemplate->getActiveSheet()->getStyle('C' . $currentIndex)->getAlignment()->setWrapText(true);
        }

        $excelSheet->setCellValue('W' . ($INITIAL_PRODUCT_ROW + $PRODUCT_COUNT), $TOTAL_COUNT);
        $excelSheet->setCellValue('AB' . ($INITIAL_PRODUCT_ROW + $PRODUCT_COUNT), $TOTAL_COUNT);
        $excelSheet->setCellValue('AL' . ($INITIAL_PRODUCT_ROW + $PRODUCT_COUNT), $TOTAL_COST);
        $excelSheet->setCellValue('N' . (26 + $PRODUCT_COUNT), $this->number2string($TOTAL_COUNT));
        $excelSheet->setCellValue('AE' . (26 + $PRODUCT_COUNT), $this->number2string($TOTAL_COST) . 'тенге');

        $excelSheet->setCellValue('AA' . (32 + $PRODUCT_COUNT), $organization);

        $excelWriter = new Xlsx($excelTemplate);
        $fileName =  Document::DOCUMENT_TYPES[$documentType] . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/waybills/' . $fileName;
        $excelWriter->save($fullPath);

        Document::create([
            'document' => $fullPath,
            'document_type' => $documentType,
            'document_number' => $documentNumber
        ]);
        return response()->json([
            'path' => $fullPath
        ]);
    }

    public function createInvoice(Request $request) {
        $contract = $request->get('contract');
        $location = $request->get('location');
        $waybill = $request->get('waybill');
        $consignee = $request->get('consignee');
        $recipient = $request->get('recipient');
        $BINLocation = $request->get('BINLocation');
        $IIK = $request->get('IIK');
        $cart = $request->get('cart');
        $product = $request->get('product');
        $TOTAL_COST = number_format(intval($this->getTotalCost($cart)), 2, ',', ' ');
        $TOTAL_COUNT = $this->getTotalCount($cart);

        $documentType = Document::DOCUMENT_INVOICE;
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('invoice_doc_template', 'xlsx');
        $excelSheet = $excelTemplate->getActiveSheet();
        $documentNumber = $this->getDocumentNumber($documentType);
        $documentDate = now()->format('d.m.Y');
        $documentName = 'Счет-фактура №' . $documentNumber . ' от ' . now()->day . ' ' . self::MONTHS_RU[now()->month - 1] . ' ' . now()->year . ' г.';

        $excelSheet->setCellValue('A1', $documentName);
        $excelSheet->setCellValue('C5', $documentDate);
        $excelSheet->setCellValue('A9', 'Договор (контракт) на поставку товаров (работ, услуг): ' . $contract);
        $excelSheet->setCellValue('A11', 'Пункт назначения поставляемых товаров (работ, услуг): ' . $location);
        $excelSheet->setCellValue('A15', 'Товарно-транспортная накладная: ' . $waybill);
        $excelSheet->setCellValue('A18', 'Грузополучатель: ' . $consignee);
        $excelSheet->setCellValue('A20', 'Получатель: ' . $recipient);
        $excelSheet->setCellValue('A21', 'БИН и адрес места нахождения получателя: ' . $BINLocation);
        $excelSheet->setCellValue('A22', ' ИИК Получателя: ' . $IIK);
        $excelSheet->setCellValue('B27', $product);
        $excelSheet->setCellValue('D27', $TOTAL_COUNT);
        $excelSheet->setCellValue('E27', $TOTAL_COST);
        $excelSheet->setCellValue('F27', $TOTAL_COST);
        $excelSheet->setCellValue('I27', $TOTAL_COST);
        $excelSheet->setCellValue('F28', $TOTAL_COST);
        $excelSheet->setCellValue('I28', $TOTAL_COST);


        $excelWriter = new Xlsx($excelTemplate);
        $fileName =  Document::DOCUMENT_TYPES[$documentType] . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/invoices/' . $fileName;
        $excelWriter->save($fullPath);

        Document::create([
            'document' => $fullPath,
            'document_type' => $documentType,
            'document_number' => $documentNumber
        ]);
        return response()->json([
            'path' => $fullPath
        ]);
    }

    public function createPaymentInvoice(Request $request) {
        $customer = $request->get('customer');
        $cart = $request->get('cart');
        $documentType = Document::DOCUMENT_INVOICE_PAYMENT;
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('invoice_payment_doc_template', 'xlsx');
        $excelSheet = $excelTemplate->getActiveSheet();
        $documentNumber = $this->getDocumentNumber($documentType);
        $documentName = 'Счет № ' . $documentNumber . 'от ' . now()->format('d.m.Y');
        $excelSheet->setCellValue('B15', $documentName);
        $excelSheet->setCellValue('F21', $customer);
        $INITIAL_PRODUCT_ROW = 25;
        $PRODUCT_COUNT = count($cart);
        $TOTAL_COST = number_format(intval($this->getTotalCost($cart)), 2, ',', ' ');
        $TOTAL_COUNT = $this->getTotalCount($cart);

        foreach ($cart as $key => $item) {
            $currentIndex = $key + $INITIAL_PRODUCT_ROW;
            try {
                if ($key > 0) {
                    $excelSheet->insertNewRowBefore($currentIndex, 1);
                    $excelSheet->duplicateStyle($excelSheet->getStyle('B25:C25'), 'B' . ($INITIAL_PRODUCT_ROW + $key) . ':C' . ($INITIAL_PRODUCT_ROW + $key));
                    $excelSheet->duplicateStyle($excelSheet->getStyle('D25:T25'), 'D' . ($INITIAL_PRODUCT_ROW + $key) . ':T' . ($INITIAL_PRODUCT_ROW + $key));
                    $excelSheet->duplicateStyle($excelSheet->getStyle('U25:W25'), 'U' . ($INITIAL_PRODUCT_ROW + $key) . ':W' . ($INITIAL_PRODUCT_ROW + $key));
                    $excelSheet->duplicateStyle($excelSheet->getStyle('Z25:AC25'), 'Z' . ($INITIAL_PRODUCT_ROW + $key) . ':AC' . ($INITIAL_PRODUCT_ROW + $key));
                    $excelSheet->duplicateStyle($excelSheet->getStyle('AD25:AG25'), 'AD' . ($INITIAL_PRODUCT_ROW + $key) . ':AG' . ($INITIAL_PRODUCT_ROW + $key));
                }
            } catch (Exception $e) {
            }
            try {
                $excelSheet->mergeCells("B" . $currentIndex . ":C" . $currentIndex);
                $excelSheet->mergeCells("D" . $currentIndex . ":T" . $currentIndex);
                $excelSheet->mergeCells("U" . $currentIndex . ":W" . $currentIndex);
                $excelSheet->mergeCells("X" . $currentIndex . ":Y" . $currentIndex);
                $excelSheet->mergeCells("Z" . $currentIndex . ":AC" . $currentIndex);
                $excelSheet->mergeCells("AD" . $currentIndex . ":AG" . $currentIndex);
            } catch (\Exception $e) {
            }

            $excelSheet->setCellValue('B' . ($currentIndex), $key + 1);
            $excelSheet->setCellValue('D' . ($currentIndex), $this->getProductName($item));
            $excelSheet->setCellValue('X' . ($currentIndex), "шт.");
            $excelSheet->setCellValue('U' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('Z' . ($currentIndex), number_format(intval($this->getProductCost($item)), 2, ',', ' '));
            $excelSheet->setCellValue('AD' . ($currentIndex), number_format(intval($this->getProductCost($item) * $item['count']), 2, ',', ' '));

            $excelTemplate->getActiveSheet()->getRowDimension($currentIndex)->setRowHeight(-1);
            $excelTemplate->getActiveSheet()->getStyle('C' . $currentIndex)->getAlignment()->setWrapText(true);
        }

        $excelSheet->setCellValue('AG' . ($INITIAL_PRODUCT_ROW + $PRODUCT_COUNT), 'Итого: ' . $TOTAL_COST);
        $excelSheet->setCellValue('B' . (28 + $PRODUCT_COUNT), 'Всего наименований ' . $TOTAL_COUNT . ', на сумму ' . $TOTAL_COST . ' тенге.');

        $excelWriter = new Xlsx($excelTemplate);
        $fileName =  Document::DOCUMENT_TYPES[$documentType] . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/invoices/' . $fileName;
        $excelWriter->save($fullPath);

        Document::create([
            'document' => $fullPath,
            'document_type' => $documentType,
            'document_number' => $documentNumber
        ]);
        return response()->json([
            'path' => $fullPath
        ]);
    }

    public function getProductCheck(Request $request) {
        $cart = $request->get('cart');
        $customer = $request->get('customer');
        $documentType = Document::DOCUMENT_PRODUCT_CHECK;
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('tovarniy_check_template', 'xls');
        $excelSheet = $excelTemplate->getActiveSheet();
        $documentNumber = $this->getDocumentNumber($documentType);
        $documentDate = now()->format('d.m.Y');
        $INITIAL_PRODUCT_ROW = 6;
        $excelSheet->setCellValue('A1', $customer);
        $excelSheet->setCellValue('A4', 'Товарный чек №' . $documentNumber . ' от ' . $documentDate . ' г.');
        foreach ($cart as $key => $item) {
            $currentIndex = $key + $INITIAL_PRODUCT_ROW;
            if ($key > 0) {
                $excelSheet->insertNewRowBefore($currentIndex, 1);
                $excelSheet->duplicateStyle($excelSheet->getStyle('B6:G6'), 'B' . ($INITIAL_PRODUCT_ROW + $key) . ':G' . ($INITIAL_PRODUCT_ROW + $key));
                $excelSheet->duplicateStyle($excelSheet->getStyle('J6:L6'), 'J' . ($INITIAL_PRODUCT_ROW + $key) . ':L' . ($INITIAL_PRODUCT_ROW + $key));
                $excelSheet->duplicateStyle($excelSheet->getStyle('M6:N6'), 'M' . ($INITIAL_PRODUCT_ROW + $key) . ':N' . ($INITIAL_PRODUCT_ROW + $key));
            }
            $excelSheet->setCellValue('A' . $currentIndex, $key + 1);
            $excelSheet->setCellValue('B' . ($currentIndex), $this->getProductName($item));
            $excelSheet->setCellValue('H' . ($currentIndex), 1);
            $excelSheet->setCellValue('I' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('J' . ($currentIndex), number_format(intval($this->getProductCost($item)), 2, ',', ' '));
            $excelSheet->setCellValue('M' . ($currentIndex),  number_format(intval($this->getProductCost($item) * $item['count']), 2, ',', ' '));
        }

        $TOTAL_COST = number_format(intval($this->getTotalCost($cart)), 2, ',', ' ');
        $TOTAL_COST_STRING = $this->number2string($this->getTotalCost($cart));
        $excelSheet->setCellValue('M' . ($INITIAL_PRODUCT_ROW + count($cart)), $TOTAL_COST);
        $excelSheet->setCellValue('E' . (9 + count($cart)), $TOTAL_COST_STRING);

        $excelWriter = new Xlsx($excelTemplate);
        $fileName =  Document::DOCUMENT_TYPES[$documentType] . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/invoices/' . $fileName;
        $excelWriter->save($fullPath);

        Document::create([
            'document' => $fullPath,
            'document_type' => $documentType,
            'document_number' => $documentNumber
        ]);
        return response()->json([
            'path' => $fullPath
        ]);
    }

    public function getPurchasePrices() {
        $products = Product::with(['sku', 'sku.batches', 'attributes', 'manufacturer'])->select(['id', 'product_name', 'product_price', 'manufacturer_id'])->get();
        $products = $products->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['product_name'],
                'attributes' => collect($item['attributes'])->map(function ($i) {
                    return $i['attribute_value'];
                })->join(', '),
                'product_price' => $item['product_price'],
                'manufacturer' => $item['manufacturer']['manufacturer_name'],
                'purchase_price' => collect($item['sku'])->map(function ($i) {
                        return collect($i['batches'])->last();
                    })->filter(function ($i) {
                        return $i != null;
                    })->last()['purchase_price'] ?? 0
            ];
        })->toArray();

        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('batches_purchase_price_template', 'xlsx');
        $excelSheet = $excelTemplate->getActiveSheet();
        $INITIAL_ROW = 2;
        foreach ($products as $key => $product) {
            $currentIndex = $key + $INITIAL_ROW;
            try {
                $excelSheet->insertNewRowBefore($currentIndex, 1);
            } catch (Exception $e) {
            }
            $excelSheet->setCellValue('A' . $currentIndex, $key + 1);
            $excelSheet->setCellValue('B' . $currentIndex, $product['name'] . ' ' . $product['attributes'] . ' ' . $product['manufacturer']);
            $excelSheet->setCellValue('C' . $currentIndex, number_format(intval($product['purchase_price']), 2, ',', ' '));
            $excelSheet->setCellValue('D' . $currentIndex, number_format(intval($product['product_price']), 2, ',', ' '));
        }

        $excelWriter = new Xlsx($excelTemplate);
        $fileName =  'ВЫГРУЗКА_ЗАКУПОВ' . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/batches/' . $fileName;
        $excelWriter->save($fullPath);

        return response()->json([
            'path' => $fullPath
        ]);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function createIherbPriceList(Request $request, ExcelService $excelService): JsonResponse {
        $cart = $request->get('cart');
        $excelFile = $excelService->loadFile('iherb_price_template');
        $excelSheet = $excelFile->getActiveSheet();
        $documentName = 'Прайс-лист IHERB от ' . now_format();
        $INITIAL_ROW = 4;
        foreach ($cart as $key => $item) {
            $currentIndex = $key + $INITIAL_ROW;
            try {
                $excelSheet->insertNewRowBefore($currentIndex, 1);
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


            $excelSheet->setCellValue('A' . ($currentIndex), $key + 1);
            $excelSheet->setCellValue('B' . ($currentIndex), $item['excel_name']);
            $excelSheet->setCellValue('O' . ($currentIndex), 'шт.');
            $excelSheet->setCellValue('R' . ($currentIndex), $item['total_quantity']);
            // Ячейка клиента
            $excelSheet->setCellValue('W' . ($currentIndex), 0);
            $validation = $excelSheet->getCell('W' . $currentIndex)->getDataValidation();
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
            $validation->setFormula2($item['total_quantity']);
            $excelSheet->setCellValue('AA' . ($currentIndex), intval($item['final_price']));

            $formula = "=PRODUCT(AA$currentIndex * W$currentIndex)";
            //$internalFormula = Calculation::getInstance()->_translateFormulaToEnglish($formula);
            $excelSheet->setCellValue('AG' . ($currentIndex), $formula);
        }

        $TOTAL_INDEX = $INITIAL_ROW + count($cart);
        $LAST_INDEX = $TOTAL_INDEX - 1;
        $formula = "=SUM(W$INITIAL_ROW:W$LAST_INDEX)";
        $excelSheet->setCellValue('T' . $TOTAL_INDEX, $formula);
        $formula = "=SUM(AG$INITIAL_ROW:AG$LAST_INDEX)";
        $excelSheet->setCellValue('AC' . $TOTAL_INDEX, $formula);
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

    private function getDocumentNumber($docType) {
        $document = Document::whereDocumentType($docType)->latest()->first();
        return $document ? $document->document_number + 1 : 1;
    }

    private function getFileType(Request $request)
    {
        $fileType = "";
        $type = $request->get('type') ?? '';
        switch ($type) {
            case "transfer":
                $fileType = 'Перемещение';
                break;
            case "sale":
                $fileType = 'Продажа';
                break;
            case "arrival":
                $fileType = "Поступление";
                break;
            default:
                $fileType = "Накладная";
                break;
        }

        return $fileType;
    }

    private function getTotalCost($cart)
    {
        $_cart = is_object($cart) ? $cart->toArray($cart) : $cart;
        return ceil(array_reduce($_cart, function ($a, $c) {
            return ($c['product_price'] - ($c['product_price'] * ($c['discount'] ?? 0) / 100)) * $c['count'] + $a;
        }, 0));
    }

    private function getProductName($item)
    {
        $attributeValues = join(' | ', array_map(function ($i) {
            return $i['attribute_value'];
        }, is_object($item['attributes']) ? $item['attributes']->toArray($item) : $item['attributes']));
        return $item['manufacturer']['manufacturer_name'] . ' ' . $item['product_name'] . ' ' . $attributeValues;
    }

    private function getProductCost($item) {
        return ceil($item['product_price'] - ($item['product_price'] * $item['discount'] / 100));
    }


    public function number2string($number)
    {
        // обозначаем словарь в виде статической переменной функции, чтобы
        // при повторном использовании функции его не определять заново

        $number = ceil($number);

        static $dic = array(

            // словарь необходимых чисел
            array(
                -2    => 'две',
                -1    => 'одна',
                1    => 'один',
                2    => 'два',
                3    => 'три',
                4    => 'четыре',
                5    => 'пять',
                6    => 'шесть',
                7    => 'семь',
                8    => 'восемь',
                9    => 'девять',
                10    => 'десять',
                11    => 'одиннадцать',
                12    => 'двенадцать',
                13    => 'тринадцать',
                14    => 'четырнадцать',
                15    => 'пятнадцать',
                16    => 'шестнадцать',
                17    => 'семнадцать',
                18    => 'восемнадцать',
                19    => 'девятнадцать',
                20    => 'двадцать',
                30    => 'тридцать',
                40    => 'сорок',
                50    => 'пятьдесят',
                60    => 'шестьдесят',
                70    => 'семьдесят',
                80    => 'восемьдесят',
                90    => 'девяносто',
                100    => 'сто',
                200    => 'двести',
                300    => 'триста',
                400    => 'четыреста',
                500    => 'пятьсот',
                600    => 'шестьсот',
                700    => 'семьсот',
                800    => 'восемьсот',
                900    => 'девятьсот'
            ),

            // словарь порядков со склонениями для плюрализации
            array(
                array('', '', ''),
                array('тысяча', 'тысячи', 'тысяч'),
                array('миллион', 'миллиона', 'миллионов'),
                array('миллиард', 'миллиарда', 'миллиардов'),
                array('триллион', 'триллиона', 'триллионов'),
                array('квадриллион', 'квадриллиона', 'квадриллионов'),
                // квинтиллион, секстиллион и т.д.
            ),

            // карта плюрализации
            array(
                2, 0, 1, 1, 1, 2
            )
        );

        // обозначаем переменную в которую будем писать сгенерированный текст
        $string = array();

        // дополняем число нулями слева до количества цифр кратного трем,
        // например 1234, преобразуется в 001234
        $number = str_pad($number, ceil(strlen($number) / 3) * 3, 0, STR_PAD_LEFT);

        // разбиваем число на части из 3 цифр (порядки) и инвертируем порядок частей,
        // т.к. мы не знаем максимальный порядок числа и будем бежать снизу
        // единицы, тысячи, миллионы и т.д.
        $parts = array_reverse(str_split($number, 3));

        try {
            foreach ($parts as $i => $part) {

                // если часть не равна нулю, нам надо преобразовать ее в текст
                if ($part > 0) {

                    // обозначаем переменную в которую будем писать составные числа для текущей части
                    $digits = array();

                    // если число треххзначное, запоминаем количество сотен
                    if ($part > 99) {
                        $digits[] = floor($part / 100) * 100;
                    }

                    // если последние 2 цифры не равны нулю, продолжаем искать составные числа
                    // (данный блок прокомментирую при необходимости)
                    if ($mod1 = $part % 100) {
                        $mod2 = $part % 10;
                        $flag = $i == 1 && $mod1 != 11 && $mod1 != 12 && $mod2 < 3 ? -1 : 1;
                        if ($mod1 < 20 || !$mod2) {
                            $digits[] = $flag * $mod1;
                        } else {
                            $digits[] = floor($mod1 / 10) * 10;
                            $digits[] = $flag * $mod2;
                        }
                    }

                    // берем последнее составное число, для плюрализации
                    $last = abs(end($digits));

                    // преобразуем все составные числа в слова
                    foreach ($digits as $j => $digit) {
                        $digits[$j] = $dic[0][$digit];
                    }

                    // добавляем обозначение порядка или валюту
                    $digits[] = $dic[1][$i][(($last %= 100) > 4 && $last < 20) ? 2 : $dic[2][min($last % 10, 5)]];

                    // объединяем составные числа в единый текст и добавляем в переменную, которую вернет функция
                    array_unshift($string, join(' ', $digits));
                }
            }

            // преобразуем переменную в текст и возвращаем из функции, ура!
            return join(' ', $string);
        } catch (\Exception $exception) {
            return $number;
        }

        // бежим по каждой части

    }


    private function getTotalCount($cart)
    {
        $_cart = is_object($cart) ? $cart->toArray($cart) : $cart;
        return array_reduce($_cart, function ($a, $c) {
            return $c['count'] + $a;
        }, 0);
    }
}
