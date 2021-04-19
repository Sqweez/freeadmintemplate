<?php

namespace App\Http\Controllers\api;

use App\Client;
use App\ClientSale;
use App\ClientTransaction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ReportService;
use App\Http\Controllers\Services\SaleService;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\v2\Report\ReportsResource;
use App\Http\Resources\SaleByCityResource;
use App\Product;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use App\v2\Models\Certificate;
use App\v2\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller {

    public function store(Request $request) {
        $saleService = new SaleService();
        try {
            \DB::beginTransaction();
            $cart = $request->get('cart');
            $store_id = $request->get('store_id');
            $client_id = $request->get('client_id');
            $discount = $request->get('discount');
            $balance = $request->get('balance');
            $user_id = $request->get('user_id');
            $partner_id = $request->get('partner_id');
            $certificate = $request->get('certificate', null);
            $used_certificate = $request->get('used_certificate', null);
            $sale = $saleService->createSale($request->except(['cart', 'certificate', 'used_certificate']));
            $saleService->createSaleProducts($sale, $store_id, $cart);
            $saleService->createClientSale($client_id, $discount, $cart, $balance, $user_id, $sale->id, $partner_id);
            $saleService->createCompanionTransaction($sale, $request->header('user_id'));
            if ($certificate) {
                $_certificate = Certificate::find($certificate['id']);
                $_certificate->sale_id = $sale->id;
                $_certificate->save();
            }
            if ($used_certificate) {
                $_certificate = Certificate::find($used_certificate['id']);
                $_certificate->used_sale_id = $sale->id;
                $_certificate->active = false;
                $_certificate->save();
            }
            \DB::commit();
            return [
                'product_quantities' => ProductBatch::whereIn('product_id', collect($cart)->pluck('id'))
                    ->whereStoreId($store_id)
                    ->groupBy('product_id')
                    ->select('product_id')
                    ->selectRaw('sum(quantity) as quantity')
                    ->get(),
                'client' => $client_id !== -1 ? new ClientResource(Client::find($client_id)) : [],
                'sale_id' => $sale->id
            ];
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ], 500);
        }
    }


    public function reports(Request $request) {
        $start = $request->get('start');
        $finish = $request->get('finish');
        $user_id = $request->get('user_id', null);
        $is_supplier = $request->has('is_supplier');
        return ReportService::getReports($start, $finish, $user_id, $is_supplier);
    }

    public function report(Sale $sale) {
        return new ReportResource($sale);
    }

    public function getTotal(Request $request) {
        $dateFilter = $request->get('date_filter');
        $sales = Sale::whereDate('created_at', '>=', $dateFilter)
            ->with(['products'])
            ->get();

        return $this->calculateTotalAmount($sales);
    }

    public function getPlanReports() {
        $today = Carbon::now();
        $startOfMonth = $today->startOf('month')->toDateString();

        $monthlySales = Sale::whereDate('created_at', '>=', $startOfMonth)
            ->with(['products:product_price,discount,sale_id'])
            ->select(['id', 'store_id', 'kaspi_red', 'balance', 'created_at'])
            ->get();


        $weeklySales = $monthlySales->filter(function ($i){
            return Carbon::parse($i->created_at)->greaterThanOrEqualTo(now()->startOfWeek());
        });

        return [
            'week' => $this->calculateTotalAmount($weeklySales),
            'month' =>  $this->calculateTotalAmount($monthlySales)
        ];
    }

    public function getReportProducts(Request $request) {
        $products_id = json_decode($request->get('products'));

        $date_start = $request->get('date_start');
        $date_finish = $request->get('date_finish');
        $user_id = $request->has('user_id') ? $request->get('user_id') : null;
        $store_id = $request->has('store_id') ? $request->get('store_id') : null;

        $saleProductQuery = SaleProduct::query()
            ->whereIn('product_id', $products_id)
            ->whereHas('sale', function ($q) use ($date_start, $date_finish, $user_id, $store_id) {
                if ($user_id) {
                    $q->whereUserId($user_id);
                }
                if ($store_id) {
                    $q->whereStoreId($store_id);
                }

                $q->whereDate('created_at', '>=', $date_start)
                    ->whereDate('created_at', '<=', $date_finish);
            })
            ->with('sale')
            ->with([
                'product',
                'product.product:id,product_name,product_price,manufacturer_id',
                'product.product.attributes:attribute_value',
                'product.attributes:attribute_value',
                'product.product.manufacturer'
            ])
            ->get()->map(function ($sale) {
                $sale['main_product_id'] = $sale['product']['id'];
                return $sale;
            })->groupBy('main_product_id')
            ->map(function ($sale, $key) {
                return [
                    'product_id' => $sale[0]['product']['id'],
                    'product_name' => $sale[0]['product']['product']['product_name'],
                    'attributes' => collect($sale[0]['product']['attributes'])->pluck('attribute_value')->merge(collect($sale[0]['product']['product']['attributes'])->pluck('attribute_value'))->join(', '),
                    'manufacturer' => $sale[0]['product']['product']['manufacturer']['manufacturer_name'],
                    'count' => count($sale),
                    'total_purchase_price' => ceil(collect($sale)->reduce(function ($a, $c) {
                        return $a + $c['purchase_price'];
                    }, 0)),
                    'total_product_price' => ceil(collect($sale)->reduce(function ($a, $c) {
                        return $a + $c['product_price'] - ($c['product_price'] * intval($c['discount']) / 100);
                    }, 0)),
                    'margin' => ceil(collect($sale)->reduce(function ($a, $c) {
                            return $a + $c['product_price'] - ($c['product_price'] * intval($c['discount']) / 100);
                        }, 0))
                        - ceil(collect($sale)->reduce(function ($a, $c) {
                            return $a + $c['purchase_price'];
                        }, 0)),
                ];
            })->values()->sortBy('product_name');
        return $saleProductQuery;
    }

    private function calculateTotalAmount($sales) {
        return collect($sales)->groupBy('store_id')
            ->map(function ($sale, $store_id) {
                return [
                    'store_id' => $store_id,
                    'amount' => (collect($sale)->reduce(function ($a, $c){
                        $price = intval(collect($c['products'])->reduce(function ($_a, $_c) use ($c) {
                            $price = $_c['product_price'] - ($_c['product_price'] * ($_c['discount'] / 100));
                            return $_a + $price;
                        }, 0));
                        if ($c['kaspi_red']) {
                            $price -= $price * Sale::KASPI_RED_PERCENT;
                        }
                        return $a + ceil($price - $c['balance']);
                    }, 0))
                ];
            });
    }


    public function cancelSale(Request $request, Sale $sale) {
        $amount = 0;
        $discount = $sale->discount;
        $products = $request->all();
        foreach ($products as $product) {
            for ($i = 0; $i < $product['count']; $i++) {
                if (isset($product['product_id']) && $product['product_id']) {
                    $saleProduct = SaleProduct::where('product_id', $product['product_id'])->where('sale_id', $sale['id'])->first();
                    $amount += $saleProduct['product_price'];
                    $productBatch = ProductBatch::find($saleProduct['product_batch_id']);
                    $productBatch->increment('quantity');
                    $productBatch->save();
                    $saleProduct->delete();
                }
                if (isset($product['certificate_id']) && $product['certificate_id']) {
                    $certificate = Certificate::find($product['certificate_id']);
                    $certificate->sale_id = 0;
                    $certificate->save();
                }
            }
        }

        $remainingProducts = SaleProduct::where('sale_id', $sale['id'])->count();

        if ($remainingProducts === 0) {
            if (intval($sale['client_id']) !== -1) {
                ClientSale::where('sale_id', $sale['id'])->first()->delete();
                ClientTransaction::where('sale_id', $sale['id'])->first()->delete();
            }
            $sale->delete();
            return null;
        }

        $_amount = $amount - ($amount * $discount / 100);

        $clientSale = ClientSale::where('sale_id', $sale['id'])->first();

        if ($sale['client_id'] !== -1) {
            $currentAmount = $clientSale['amount'] - $_amount;
            $clientSale->update(['amount' => $currentAmount]);
            $clientTransaction = ClientTransaction::where('sale_id', $sale['id'])->first();
            $newAmount = $clientTransaction['amount'] - $_amount * 0.01;
            $clientTransaction->update(['amount' => $newAmount]);
        }

        return new ReportsResource($sale);

    }

    public function update(Request $request, $id) {
        $sale = Sale::findOrFail($id);
        $sale->update($request->all());
        return new ReportsResource(Sale::report()
            ->whereKey($id)
            ->first()
        );
    }

}
