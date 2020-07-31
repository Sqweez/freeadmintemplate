<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ExcelService;
use App\Http\Resources\ProductRevisionResource;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RevisionController extends Controller
{
    public function getRevisionProducts(Request $request) {
        $products = collect(ProductRevisionResource::collection(Product::all()))->groupBy('categories')->collapse();
        $excelService = new ExcelService();
        return $excelService->createRevisionExcel($products);
    }
}
