<?php

namespace App\Http\Controllers;

use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use App\v2\Models\ProductSku;
use DNS1D;
use DNS2D;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function printBarcode(Request $request, $id) {
        $count = intval($request->get('count', 1));
        $productSku = ProductSku::findOrFail($id);
        $barcodes = [];
        for ($i = 0; $i < $count; $i++) {
            $barcodes[] = [
                'html' => DNS1D::getBarcodeSVG($productSku->product_barcode, 'EAN13', 2, 35, 'black', false),
                'barcode' => $productSku->product_barcode,
            ];
        }
        return view('print.barcode', compact('barcodes'));
    }

    public function printPrice(Request $request, $id) {
        $count = intval($request->get('count', 1));
        $type = $request->get('type', 'price');
        $productSku = ProductSku::findOrFail($id);
        $productSku->load('product');
        $productSku->load('product.attributes.attribute_name');
        $productSku->load('product.manufacturer');
        $productSku->load('attributes.attribute_name');
        $barcode = DNS1D::getBarcodeSVG($productSku->product_barcode, 'EAN13', 2, 10, 'black', false);
        return view('print.price', compact('count', 'productSku', 'type', 'barcode'));
    }

    public function printCheck($sale, Request $request) {
        $report = ReportsResource::make(Sale::report()->whereKey($sale)->first())->toArray($request);
        if ($report['payment_type'] === 2) {
            $report['final_price'] += $report['final_price'] * Sale::KASPI_RED_PERCENT;
        }
        return view('print.check', [
            'report' => (object) $report,
        ]);
    }
}
