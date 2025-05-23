<?php

namespace App\Http\Controllers\api;

use App\Actions\Sale\UpdateSaleAction;
use App\Client;
use App\ClientSale;
use App\ClientTransaction;
use App\DTO\Reports\ReportOptionsDTO;
use App\Http\Controllers\Services\ReportService;
use App\Http\Controllers\Services\SaleService;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\v2\Report\ReportsResource;
use App\Jobs\Notifications\SendDeliveryNotificationJob;
use App\Jobs\Notifications\SendWholesaleOrderNotificationJob;
use App\Jobs\Sale\CheckEliteClubSaleJob;
use App\Manufacturer;
use App\Product;
use App\ProductBatch;
use App\Promocode;
use App\Sale;
use App\SaleProduct;
use App\Service\Sales\EliteClubSaleReportService;
use App\User;
use App\UserRole;
use App\v2\Models\BarterBalance;
use App\v2\Models\Booking;
use App\v2\Models\BrandMotivation;
use App\v2\Models\Certificate;
use App\v2\Models\EliteGiftGiveaway;
use App\v2\Models\Preorder;
use App\v2\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SaleController extends BaseApiController
{

    public function store(Request $request, SaleService $saleService)
    {
        try {
            // @TODO 2024-08-26T15:50 rework controller, move it to lvl 2 api
            \DB::beginTransaction();
            $cart = $request->get('cart');
            $paid_by_barter = $request->get('paid_by_barter', 0);
            $store_id = $request->get('store_id');
            $client_id = $request->get('client_id');
            $discount = $request->get('discount');
            $balance = $request->get('balance');
            $user_id = $request->get('user_id');
            $partner_id = $request->get('partner_id');
            $certificate = $request->get('certificate', null);
            $used_certificate = $request->get('used_certificate', null);
            $promocode_id = $request->get('promocode_id', null);
            $preorder = $request->get('preorder', null);
            $sale = $saleService->createSale(
                $request->except(
                    ['cart', 'certificate', 'used_certificate', 'preorder', 'paid_by_barter', 'barter_balance', 'is_elite_gift_giveaway']
                )
            );
            $client = Client::find($client_id);
            // @TODO 2023-08-02T14:23:33 rework sale controller
            if ($paid_by_barter) {
                $barterBalance = $request->get('barter_balance', 0);
                $sale->update([
                    'paid_by_barter_balance' => $barterBalance
                ]);

                $client->transactions()->create([
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'amount' => $barterBalance * -1
                ]);

                while ($barterBalance > 0) {
                    $barterBalanceEntity = BarterBalance::whereClientId($client_id)->where('amount', '>', 0)->where(
                        'is_active',
                        true
                    )->first();

                    if (!$barterBalanceEntity) {
                        $barterBalance = 0;
                    } else {
                        $amount = $barterBalanceEntity->amount;
                        $remaining = $amount - $barterBalance;
                        $barterBalanceEntity->update([
                            'amount' => max(0, $remaining),
                            'is_active' => $remaining > 0,
                        ]);
                        $barterBalance = $remaining * -1;
                    }
                }
            }

            $saleService->createSaleProducts($sale, $store_id, $cart);
            $pr = Promocode::find($promocode_id);
            $saleService->createClientSale(
                $client_id,
                $discount,
                $cart,
                $balance,
                $user_id,
                $sale->id,
                optional($pr)->client_id,
                $request->get('payment_type')
            );
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

            if ($preorder) {
                $_preorder = Preorder::find($preorder['id']);
                $_preorder->status = 1;
                $_preorder->sale_id = $sale->id;
                $_preorder->save();
            }
            // @TODO rework later
            if ($request->get('is_elite_gift_giveaway') === true && $client_id !== -1) {
                $gift = collect($cart)->where('is_free_elite_gift', true)->where('discount', 100)->first();
                if ($gift) {
                    EliteGiftGiveaway::create([
                        'client_id' => $client_id,
                        'sale_id' => $sale->id,
                        'gift_id' => $gift['id']
                    ]);
                }
            }
            \DB::commit();
            if ($sale->is_opt) {
                SendWholesaleOrderNotificationJob::dispatch($sale);
            }
            if ($client_id !== -1) {
                CheckEliteClubSaleJob::dispatch($sale);
            }
            return [
                'product_quantities' => ProductBatch::query()->whereIn(
                    'product_id',
                    collect($cart)->pluck('id')
                )->whereStoreId($store_id)->groupBy('product_id')->select('product_id')->selectRaw(
                    'sum(quantity) as quantity'
                )->get(),
                'client' => $client_id !== -1 ? new ClientResource(Client::find($client_id)) : [],
                'sale_id' => $sale->id
            ];
        } catch (\Exception $exception) {
            \DB::rollBack();
            \Log::info('error:'.$exception->getMessage());
            return response()->json([
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ], 500);
        }
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function reports(Request $request, ReportService $reportService): AnonymousResourceCollection
    {
        $user = $this->retrieveAnyUser();
        abort_if(!$user, 403);
        $reportOptionsDTO = new ReportOptionsDTO($request->all());
        $reportOptionsDTO->setUser($user);
        $reportsQuery = $reportService->getReportsQuery($reportOptionsDTO);
        return $reportService->toResource($reportsQuery);
    }

    /**
     * @throws Exception
     */
    public function downloadReportExcel(Request $request, ReportService $reportService): string
    {
        $user = $this->retrieveAnyUser();
        abort_if(!$user, 403);
        $reportOptionsDTO = new ReportOptionsDTO($request->all());
        $reportOptionsDTO->setUser($user);
        $reportsQuery = $reportService->getReportsQuery($reportOptionsDTO);
        return $reportService->getExcelProductReport($reportsQuery);
    }

    /**
     */
    public function downloadEliteReportExcel(
        Request $request,
        ReportService $reportService,
        EliteClubSaleReportService $eliteClubSaleReportService):
        string
    {
        $user = $this->retrieveAnyUser();
        abort_if(!$user, 403);
        $reportOptionsDTO = new ReportOptionsDTO($request->all());
        $reportOptionsDTO->setUser($user);
        $reportsQuery = $reportService->getReportsQuery($reportOptionsDTO);
        return $eliteClubSaleReportService->getEliteExcelReport($reportsQuery);
    }

    public function getSummaryReports(Request $request)
    {
        try {
            $start = today()->subDays(3)->startOfDay();
            $finish = today()->subDay()->endOfDay();

            $saleProductsSubQuery = DB::table('sale_products')->select(
                'sale_id',
                DB::raw('SUM(product_price - (discount / 100 * product_price)) as total_product_price'),
                DB::raw('SUM(purchase_price) as total_purchase_price'),
                DB::raw(
                    'SUM(CASE WHEN discount = 100 THEN 0 ELSE (product_price - (discount / 100 * product_price)) - purchase_price END) as total_margin'
                )
            )->groupBy('sale_id');

            $results = DB::table('sales')/*->selectRaw('
                    SUM(sps.total_product_price) as total_product_price,
                    SUM(sps.total_purchase_price) as total_purchase_price,
                    SUM(sps.total_margin) as total_margin,
                    SUM(purchased_certs.amount) as total_certificates_sold,
                    SUM(used_certificates.amount) as total_certificates_paid,
                    SUM(sales.balance) as total_balance,
                    (
                        SUM(sps.total_product_price)
                        + SUM(purchased_certs.amount)
                        - SUM(used_certificates.amount)
                        - SUM(sales.balance)
                    ) * AVG(CASE WHEN sales.kaspi_red THEN 0.89 ELSE 1 END) as total_final_price
                ')*/ ->selectRaw(
                '
                    SUM(sps.total_product_price) as total_product_price,
                    SUM(sps.total_purchase_price) as total_purchase_price,
                    SUM(sps.total_margin) as total_margin,
                    SUM(purchased_certs.amount) as total_certificates_sold,
                    SUM(used_certificates.amount) as total_certificates_paid,
                    SUM(sales.balance) as total_balance,
                    (
                        SUM(sps.total_product_price)
                         - SUM(sales.balance)
                         + SUM(purchased_certs.amount)
                         - SUM(sales.promocode_fixed_amount)
                    ) as total_final_price'
            )
                // Добавить сертификат - закуп, т.е. сертификат - финальная стоимость товара
                ->leftJoinSub($saleProductsSubQuery, 'sps', function ($join) {
                    $join->on(
                        'sales.id',
                        '=',
                        'sps.sale_id'
                    ); // Убедитесь, что это условие соединения корректно, так как подзапрос не содержит sale_id
                })->selectRaw('COUNT(*) as sales_count')->leftJoin('certificates as purchased_certs', function ($join) {
                    $join->on('sales.id', '=', 'purchased_certs.sale_id')->where(function ($query) {
                        $query->whereNull('purchased_certs.used_sale_id')->orWhere(
                            'purchased_certs.used_sale_id',
                            '=',
                            0
                        );
                    });
                })->leftJoin(
                    'certificates as used_certificates',
                    'sales.id',
                    '=',
                    'used_certificates.used_sale_id'
                )->whereDate('sales.created_at', '>=', Carbon::parse('2023-10-01')->startOfDay())->whereDate(
                    'sales.created_at',
                    '<=',
                    Carbon::parse('2023-10-01')->endOfDay()
                )->get();


            return [
                'results' => $results,
                'start' => $start,
                'end' => $finish,
            ];
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }


    public function getSaleById(Sale $sale): ReportsResource
    {
        return ReportsResource::make($sale);
    }

    public function report(Sale $sale)
    {
        return new ReportResource($sale);
    }

    public function getTotal(Request $request)
    {
        $dateFilter = $request->get('date_filter');
        $role = $request->get('role');
        $store_id = $request->get('store_id');

        $sales = Sale::query()->where('created_at', '>=', Carbon::parse($dateFilter)->startOfDay())->where(
            'is_confirmed',
            true
        )->with([
            'products' => function ($subQuery) {
                return $subQuery->whereHas(
                    'product.product', fn($q) => $q->where('manufacturer_id', '!=', __hardcoded(698))
                );
            }
        ])->with(['certificate'])->when($role === UserRole::ROLE_FRANCHISE, function ($query) use ($store_id) {
            return $query->where('store_id', $store_id);
        })->get();

        $farmaSalesQuery = Sale::query()->where('created_at', '>=', Carbon::parse($dateFilter)->startOfDay())->where(
            'is_confirmed',
            true
        )->whereHas('products', function ($subQuery) {
            return $subQuery->whereHas('product.product', fn($q) => $q->where('manufacturer_id', __hardcoded(698)));
        })->with([
            'products' => function ($subQuery) {
                return $subQuery->whereHas(
                    'product.product', fn($q) => $q->where('manufacturer_id', __hardcoded(698))
                );
            }
        ])->with(['certificate'])->when($role === UserRole::ROLE_FRANCHISE, function ($query) use ($store_id) {
            return $query->where('store_id', $store_id);
        })->get();

        $bookingSales = collect([
            9999 => [
                'store_id' => 9999,
                'amount' => intval(Booking::whereDate('created_at', '>=', $dateFilter)->sum('paid_sum'))
            ]
        ]);

        $farmaSales = collect([
            7777 => [
                'store_id' => 7777,
                'amount' => $this->getTotalAmount($farmaSalesQuery)
            ]
        ]);

        return $this->calculateTotalAmount($sales)->mergeRecursive($bookingSales)->mergeRecursive($farmaSales)->groupBy(
            'store_id'
        )->map(function ($item) {
            return collect($item)->first();
        });
    }

    public function getPlanReports(Request $request): array
    {
        $today = now();
        $startOfMonth = $today->startOf('month')->toDateString();

        $monthlySales = Sale::whereDate('created_at', '>=', $startOfMonth)->where(
            'store_id',
            $request->get('store_id', 1)
        )->where('user_id', '!=', 2)->where('payment_type', '!=', 4)->with(
            ['products:product_price,discount,sale_id']
        )->select(['id', 'store_id', 'kaspi_red', 'balance', 'created_at'])->get();


        $weeklySales = $monthlySales->filter(function ($i) {
            return Carbon::parse($i->created_at)->greaterThanOrEqualTo(now()->startOfWeek());
        });

        $todaySales = $monthlySales->filter(function ($i) {
            return Carbon::parse($i->created_at)->greaterThanOrEqualTo(now()->startOfDay());
        });

        return [
            'week' => $this->calculateTotalAmount($weeklySales),
            'month' => $this->calculateTotalAmount($monthlySales),
            'today' => $this->calculateTotalAmount($todaySales)
        ];
    }

    public function getReportProducts(Request $request)
    {
        $products_id = json_decode($request->get('products'));

        $date_start = $request->get('date_start');
        $date_finish = $request->get('date_finish');
        $user_id = $request->has('user_id') ? $request->get('user_id') : null;
        $store_id = $request->has('store_id') ? $request->get('store_id') : null;

        return SaleProduct::query()->whereIn('product_id', $products_id)->whereHas(
            'sale', function ($q) use ($date_start, $date_finish, $user_id, $store_id) {
            if ($user_id) {
                $q->whereUserId($user_id);
            }
            if ($store_id) {
                $q->whereStoreId($store_id);
            }

            $q->whereDate('created_at', '>=', $date_start)->whereDate('created_at', '<=', $date_finish);
        }
        )->with('sale')->with([
            'product',
            'product.product:id,product_name,product_price,manufacturer_id',
            'product.product.attributes:attribute_value',
            'product.attributes:attribute_value',
            'product.product.manufacturer'
        ])->get()->map(function ($sale) {
            $sale['main_product_id'] = $sale['product']['id'];
            return $sale;
        })->groupBy('main_product_id')->map(function ($sale, $key) {
            $totalPurchasePrice = (collect($sale)->reduce(function ($a, $c) {
                return $a + $c['purchase_price'];
            }, 0));
            $totalProductPrice = ceil(collect($sale)->reduce(function ($a, $c) {
                $currentPrice = $c['product_price'] - ($c['product_price'] * intval($c['discount']) / 100);
                if ($c['sale']['kaspi_red']) {
                    $currentPrice -= $currentPrice * Sale::KASPI_RED_PERCENT;
                }
                if ($c['sale']['balance'] > 0) {
                    $currentPrice -= $c['balance'];
                }
                return $a + $currentPrice;
            }, 0));
            return [
                'product_id' => $sale[0]['product']['id'],
                'product_name' => $sale[0]['product']['product']['product_name'],
                'attributes' => collect($sale[0]['product']['attributes'])->pluck('attribute_value')->merge(
                    collect($sale[0]['product']['product']['attributes'])->pluck('attribute_value')
                )->join(', '),
                'manufacturer' => $sale[0]['product']['product']['manufacturer']['manufacturer_name'],
                'manufacturer_id' => $sale[0]['product']['product']['manufacturer']['id'],
                'count' => count($sale),
                'total_purchase_price' => $totalPurchasePrice,
                'total_product_price' => $totalProductPrice,
                'margin' => $totalProductPrice > 0 ? $totalProductPrice - $totalPurchasePrice : 0,
            ];
        })->values()->sortBy('product_name');
    }

    private function calculateTotalAmount($sales): Collection
    {
        $kaspiSales = collect($sales)->filter(function ($i) {
            return $i['payment_type'] === 4;
        })->values();

        $internetSales = collect($sales)->filter(function ($i) {
            return $i['user_id'] === 2;
        })->values();

        $kaspiAndWebSales = $kaspiSales->mergeRecursive($internetSales);
        $otherSales = collect($sales)->diff($kaspiAndWebSales)->all();

        $retailSales = collect($otherSales)->filter(function ($sale) {
            return !$sale['is_opt'];
        })->values()->groupBy('store_id')->map(function ($sale, $store_id) {
            return [
                'store_id' => $store_id,
                'amount' => $this->getTotalAmount($sale)
            ];
        });

        $optSales = collect($otherSales)->filter(function ($sale) {
            return $sale['is_opt'];
        })->values();

        $optSales = collect([
            7845 => [
                'store_id' => 7845,
                'amount' => $this->getTotalAmount($optSales)
            ]
        ]);

        $kaspiSales = collect([
            5555 => [
                'store_id' => 5555,
                'amount' => $this->getTotalAmount($kaspiSales)
            ]
        ]);

        $internetSales = collect([
            -1 => [
                'store_id' => -1,
                'amount' => $this->getTotalAmount($internetSales)
            ]
        ]);

        return $kaspiSales->mergeRecursive($retailSales)->mergeRecursive($internetSales)->mergeRecursive(
            $optSales
        )->groupBy('store_id')->map(function ($item) {
            return collect($item)->first();
        });
    }

    private function getTotalAmount($sales)
    {
        return (collect($sales)->reduce(function ($a, $c) {
            return $a + $c->final_price;
        }, 0));
    }

    public function editSaleList(Sale $sale, Request $request, UpdateSaleAction $action)
    {
        $action->handle($sale, $request);
        return new ReportsResource(Sale::find($sale->id));
    }

    /**
     * @throws \Exception
     */
    public function cancelSale(Request $request, Sale $sale)
    {
        $amount = 0;
        $discount = $sale->discount;
        $products = $request->all();
        foreach ($products as $product) {
            for ($i = 0; $i < $product['count']; $i++) {
                if (isset($product['product_id']) && $product['product_id']) {
                    $saleProduct = SaleProduct::where('product_id', $product['product_id'])->where(
                        'sale_id',
                        $sale['id']
                    )->first();
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
                $clientSale = ClientSale::where('sale_id', $sale['id'])->first();
                $clientTransaction = ClientTransaction::where('sale_id', $sale['id'])->first();
                $client = Client::find($sale['client_id']);
                $client->cached_balance -= $clientTransaction->amount;
                $client->cached_total_sale_amount -= $clientSale->amount;
                $client->save();
                $clientSale->delete();
                $clientTransaction->delete();
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

    public function cancelSaleFull(Sale $sale): string
    {
        $products = $sale->products;
        $products->each(
        /**
         * @throws \Exception
         */ function (SaleProduct $product) use ($sale) {
            ProductBatch::where('id', $product->product_batch_id)->increment('quantity');
            $product->delete();
        }
        );
        ClientSale::where('sale_id', $sale['id'])->delete();
        ClientTransaction::where('sale_id', $sale['id'])->delete();
        $sale->delete();
        return 'Продажа была отменена!';
    }

    public function update(Request $request, $id): ReportsResource
    {
        $sale = Sale::findOrFail($id);
        $sale->update($request->all());
        return new ReportsResource(
            Sale::report()->whereKey($id)->first()
        );
    }

    public function getMotivationReport(Request $request)
    {
        $motivations = BrandMotivation::all();
        $motivations = $motivations->map(function ($item) {
            return [
                'name' => Manufacturer::whereIn('id', $item['brands'])->get()->pluck('manufacturer_name')->join(' | '),
                'motivation' => $this->getBrandsMotivation($item['brands']),
                'amount' => $item['amount']
            ];
        });

        $sellers = User::sellers()->with('store:id,name')->select(['id', 'store_id', 'name', 'role_id'])->get();

        return $sellers->map(function ($seller) use ($motivations) {
            return [
                'id' => $seller['id'],
                'name' => $seller['name'].' | '.$seller['store']['name'],
                'motivations' => collect($motivations)->map(function ($motivation) use ($seller) {
                    $currentMotivation = collect($motivation['motivation'])->filter(function ($item) use ($seller) {
                        return $seller['id'] === $item['user_id'];
                    })->first() ?? ['sum' => 0, 'percent' => 0];
                    $currentPlan = collect($motivation['amount'])->filter(function ($i) use ($seller) {
                        return $i['user_id'] == $seller['id'];
                    })->first()['amount'] ?? 0;
                    return [
                        'name' => $motivation['name'],
                        'plan' => $currentPlan,
                        'sum' => $currentMotivation['sum'],
                        'percent' => round(
                            ($currentMotivation['sum'] == 0 || $currentPlan == 0) ? 0 : 100 * $currentMotivation['sum'] / $currentPlan
                        )
                    ];
                })
            ];
        })->filter(function ($item) {
            return collect($item['motivations'])->filter(function ($i) {
                    return $i['plan'] > 0;
                })->count() > 0;
        })->values()->all();
    }

    private function getBrandsMotivation($brands)
    {
        $products_id = Product::whereIn('manufacturer_id', $brands)->select(['id'])->get();
        $products_id = ProductSku::whereIn('product_id', $products_id)->select(['id'])->get()->pluck('id');
        $date_start = now()->startOfMonth();
        $date_finish = now()->endOfMonth();
        return SaleProduct::query()->whereIn('product_id', $products_id)->whereHas(
            'sale', function ($q) use ($date_start, $date_finish) {
            $q->whereDate('created_at', '>=', $date_start)->whereDate(
                'created_at',
                '<=',
                $date_finish
            )->physicalSales();
        }
        )->with('sale')->get()->map(function ($sale) {
            $sale['user_id'] = $sale['sale']['user_id'];
            $sale['discount'] = $sale['sale']['discount'];
            return $sale;
        })->groupBy('user_id')->map(function ($sale, $key) {
            $totalSum = ceil(collect($sale)->reduce(function ($a, $c) use ($key) {
                return $a + $c['product_price'] - $c['product_price'] * ($c['discount'] / 100);
            }, 0));
            return [
                'sum' => $totalSum,
                'user_id' => $key
            ];
        })->values()->all();
    }

    public function getSaleTypes(): Collection
    {
        return collect(Sale::PAYMENT_TYPES)->map(function ($item, $key) {
            return [
                'id' => $key,
                'name' => collect($item)->first()
            ];
        });
    }

    public function sendTelegramOrderMessage(Sale $sale): JsonResponse
    {
        try {
            SendDeliveryNotificationJob::dispatch($sale);
            return response()->json([], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 200);
        }
    }

    public function confirmSale(Sale $sale)
    {
        $sale->update(['is_confirmed' => true]);
        return 'Продажа подтверждена!';
    }
}
