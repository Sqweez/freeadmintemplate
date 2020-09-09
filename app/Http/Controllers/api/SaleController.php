<?php

namespace App\Http\Controllers\api;

use App\Client;
use App\ClientSale;
use App\ClientTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\SaleByCityResource;
use App\Product;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller {

    private $DECREASE = -1;
    private $INCREASE = 1;

    public function store(Request $request) {
        $_cart = $request->get('cart');
        $_sale = $request->except('cart');
        $sale = Sale::create($_sale);
        $sale_id = $sale['id'];
        $store_id = $request->get('store_id');
        $client_id = $request->get('client_id');
        $this->parseCart($sale_id, $store_id, $_cart);
        $this->createClientSale($sale_id, $request->all());
        return ['products' => ProductResource::collection(Product::find(array_map(function ($i) {
            return $i['id'];
        }, $_cart))), 'client' => $client_id === -1 ? [] : new ClientResource(Client::find($request->get('client_id'))), 'sale_id' => $sale_id];
    }

    private function parseCart($sale_id, $store_id, $cart = []) {
        $products = [];
        foreach ($cart as $item) {
            for ($i = 0; $i < $item['count']; $i++) {
                $product_batch = ProductBatch::where('product_id', $item['id'])->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
                $product_sale = ['product_batch_id' => $product_batch['id'], 'product_id' => $item['id'], 'sale_id' => $sale_id, 'purchase_price' => $product_batch['purchase_price'], 'product_price' => $item['product_price']];

                $this->createProductSale($product_sale);

                $this->changeCount($product_batch, $this->DECREASE);
            };
        }
    }

    private function createProductSale($cartItem) {
        SaleProduct::create($cartItem);
    }


    private function changeCount(ProductBatch $productBatch, $MODE) {
        $quantity = $productBatch['quantity'] + (1 * $MODE);
        $productBatch->update(['quantity' => $quantity]);
    }

    private function createClientSale($id, $request) {
        $client_id = $request['client_id'];
        if ($client_id === -1) {
            return;
        }
        $discount = $request['discount'];
        $cart = $request['cart'];
        $_amount = array_reduce($cart, function ($c, $i) {
            return $c + ($i['product_price'] * $i['count']);
        });

        $amount = $_amount - ($_amount * $discount / 100);

        ClientSale::create(['client_id' => $client_id, 'amount' => $amount, 'sale_id' => $id,]);
        ClientTransaction::create(['client_id' => $client_id, 'sale_id' => $id, 'amount' => $amount * 0.01, 'user_id' => $request['user_id']]);

        if ($request['balance'] > 0) {
            ClientTransaction::create(['client_id' => $client_id, 'sale_id' => $id, 'amount' => $request['balance'] * -1, 'user_id' => $request['user_id']]);
        }

        if (isset($request['partner_id']) && $request['partner_id']) {
            ClientTransaction::create(['client_id' => $request['partner_id'], 'sale_id' => $id, 'amount' => $amount * 0.05, 'user_id' => $request['user_id']]);
        }
    }

    public function reports(Request $request) {

        $FILTERS = ['ALL_TIME' => 1, 'CURRENT_MONTH' => 2, 'TODAY' => 3, 'CUSTOM_FILTER' => 4, 'LAST_3_DAYS' => 5,];

        $filter = intval($request->get('filter')) ?? 3;
        $start = $request->get('start') ?? null;
        $finish = $request->get('finish') ?? null;

        $dates = [];

        $currentDate = Carbon::today();
        switch ($filter) {
            case $FILTERS['TODAY']:
            {
                $dates = [$currentDate->toDateString(),];
                break;
            }
            case $FILTERS['CURRENT_MONTH']:
            {
                $dates = [$currentDate->subDays(30)->toDateString(),];
                break;
            }
            case $FILTERS['ALL_TIME']:
            {
                $dates = [Carbon::createFromTimestamp(0)->toDateString()];
                break;
            }

            case $FILTERS['LAST_3_DAYS']:
            {
                $dates = [$currentDate->subDays(3)->toDateString()];
                break;
            }

            case $FILTERS['CUSTOM_FILTER']:
            {
                $dates = [Carbon::parse($start), Carbon::parse($finish)];
                break;
            }

            default:
            {
                $dates = [Carbon::now()->toDateString()];
            }
        }

        if (count($dates) === 1) {
            $dates[] = Carbon::now()->toDateString();
        }

        return ReportResource::collection(
            Sale::with(
                ['client', 'user', 'store', 'products', 'products.products', 'products.products.manufacturer', 'products.products.attributes']
            )
                ->whereDate('created_at', '>=', $dates[0])
                ->whereDate('created_at', '<=', $dates[1])->orderBy('created_at', 'desc')->get());

    }

    public function report(Sale $sale) {
        return new ReportResource($sale);
    }

    public function getTotal(Request $request) {
        $dateFilter = $request->get('date_filter') ?? 'today';
        $dates = $this->getDatesFilters($dateFilter);
        $sales = Sale::whereDate('created_at', '>=', $dates[0])->whereDate('created_at', '<=', $dates[1])->with(['products', 'products.products'])->get();
        return SaleByCityResource::collection($sales);
    }

    public function getPlanReports() {
        $today = Carbon::now();
        $startOfWeek = $today->startOf('week')->toDateString();
        $startOfMonth = $today->startOf('month')->toDateString();

        return ['week' => SaleByCityResource::collection(Sale::whereDate('created_at', '>=', $startOfWeek)->get()), 'month' => SaleByCityResource::collection(Sale::whereDate('created_at', '>=', $startOfMonth)->get()),];
    }

    private function getDatesFilters($dateFilter) {
        $dates = [];
        $currentDate = Carbon::today();
        switch ($dateFilter) {
            case 'today':
            {
                $dates = [$currentDate->toDateString(),];
                break;
            }
            case 'week':
            {
                $dates = [$currentDate->subDays(7)->toDateString(),];
                break;
            }
            case 'month':
            {
                $dates = [$currentDate->subDays(30)->toDateString(),];
                break;
            }
            case '3months':
            {
                $dates = [$currentDate->subDays(90)->toDateString()];
                break;
            }
            case 'alltime':
            {
                $dates = [Carbon::createFromTimestamp(0)->toDateString()];
                break;
            }
            default:
            {
                $dates = [Carbon::now()->toDateString()];
            }
        }

        array_push($dates, Carbon::now()->toDateString());
        return $dates;

    }

    public function cancelSale(Request $request, Sale $sale) {
        $amount = 0;
        $discount = $sale['discount'];
        $products = $request->all();
        foreach ($products as $product) {
            for ($i = 0; $i < $product['count']; $i++) {
                $saleProduct = SaleProduct::where('product_id', $product['product_id'])->where('sale_id', $sale['id'])->first();
                $amount += $saleProduct['product_price'];
                $productBatch = ProductBatch::find($saleProduct['product_batch_id']);
                $this->changeCount($productBatch, $this->INCREASE);
                $saleProduct->delete();
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

        return new ReportResource($sale);

    }

}
