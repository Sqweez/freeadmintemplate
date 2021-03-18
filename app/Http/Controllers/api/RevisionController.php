<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ExcelService;
use App\Http\Resources\ProductRevisionResource;
use App\Http\Resources\RevisionInfoResource;
use App\Http\Resources\RevisionResource;
use App\Product;
use App\Revision;
use App\RevisionProducts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RevisionController extends Controller
{
    public function getRevisionProducts(Request $request) {
        $products = collect(ProductRevisionResource::collection(Product::all()))
            ->groupBy(['category', 'product_name'])->collapse()->collapse();
        $excelService = new ExcelService();
        return $excelService->createRevisionExcel($products);
    }

    public function createRevision(Request $request) {
        $excelService = new ExcelService();
        $products = $excelService->parseRevisionExcel($request->get('file'));
        $revision = Revision::create($request->except('file'));
        foreach ($products as $product) {
            if (intval($product['stock_quantity']) !== intval($product['fact_quantity'])) {
                RevisionProducts::create([
                    'revision_id' => $revision->id,
                    'product_id' => $product['id'],
                    'stock_quantity' => $product['stock_quantity'],
                    'fact_quantity' => $product['fact_quantity'],
                ]);
            }
        }
        return $products;
    }

    public function getRevisions(Request $request) {
        return RevisionResource::collection(Revision::all());
    }

    public function getRevisionInfo(Revision $revision) {
        return RevisionInfoResource::collection($revision->revision_products->all());
    }
}
