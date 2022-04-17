<?php

namespace App\Http\Controllers\api;

use App\Client;
use App\ClientSale;
use App\ClientTransaction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ReportService;
use App\Http\Controllers\Services\SaleService;
use App\Http\Controllers\Services\TelegramService;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\v2\Report\ReportsResource;
use App\Http\Resources\SaleByCityResource;
use App\Manufacturer;
use App\Product;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use App\Store;
use App\User;
use App\UserRole;
use App\v2\Models\Booking;
use App\v2\Models\BrandMotivation;
use App\v2\Models\Certificate;
use App\v2\Models\Preorder;
use App\v2\Models\ProductSku;
use Carbon\Carbon;
use Facade\FlareClient\Report;
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
            $preorder = $request->get('preorder', null);
            $sale = $saleService->createSale($request->except(['cart', 'certificate', 'used_certificate', 'preorder']));
            $saleService->createSaleProducts($sale, $store_id, $cart);
            $saleService->createClientSale($client_id, $discount, $cart, $balance, $user_id, $sale->id, $partner_id, $request->get('payment_type'));
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
            \Log::info('error:' . $exception->getMessage());
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
        $store_id = $request->get('store_id', null);
        $manufacturer_id = $request->get('manufacturer_id', null);
        return ReportService::getReports($start, $finish, $user_id, $is_supplier, $store_id, $manufacturer_id);
    }

    public function report(Sale $sale) {
        return new ReportResource($sale);
    }

    public function getTotal(Request $request) {
        $dateFilter = $request->get('date_filter');
        $role = $request->get('role');
        $store_id = $request->get('store_id');

        $salesQuery = Sale::query()
            ->whereDate('created_at', '>=', $dateFilter)
            ->with(['products'])
            ->with(['certificate']);

        if ($role === UserRole::ROLE_FRANCHISE) {
            $salesQuery = $salesQuery
                ->where('store_id', $store_id);
        }

        $sales = $salesQuery->get();

        $bookingSales = collect([
            9999 => [
                'store_id' => 9999,
                'amount' => intval(Booking::whereDate('created_at', '>=', $dateFilter)->sum('paid_sum'))
            ]
        ]);

        return $this->calculateTotalAmount($sales)
            ->mergeRecursive($bookingSales)
            ->groupBy('store_id')
            ->map(function ($item) {
                return collect($item)->first();
            });
    }

    public function getPlanReports() {
        $today = Carbon::now();
        $startOfMonth = $today->startOf('month')->toDateString();

        $monthlySales = Sale::whereDate('created_at', '>=', $startOfMonth)
            ->where('user_id', '!=', 2)
            ->where('payment_type', '!=', 4)
            ->with(['products:product_price,discount,sale_id'])
            ->select(['id', 'store_id', 'kaspi_red', 'balance', 'created_at'])
            ->get();


        $weeklySales = $monthlySales->filter(function ($i){
            return Carbon::parse($i->created_at)->greaterThanOrEqualTo(now()->startOfWeek());
        });

        $todaySales = $monthlySales->filter(function ($i) {
            return Carbon::parse($i->created_at)->greaterThanOrEqualTo(now()->startOfDay());
        });

        return [
            'week' => $this->calculateTotalAmount($weeklySales),
            'month' =>  $this->calculateTotalAmount($monthlySales),
            'today' => $this->calculateTotalAmount($todaySales)
        ];
    }

    public function getReportProducts(Request $request) {
        $products_id = json_decode($request->get('products'));

        $date_start = $request->get('date_start');
        $date_finish = $request->get('date_finish');
        $user_id = $request->has('user_id') ? $request->get('user_id') : null;
        $store_id = $request->has('store_id') ? $request->get('store_id') : null;

        return SaleProduct::query()
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
                $totalPurchasePrice = (collect($sale)->reduce(function ($a, $c) {
                    return $a + $c['purchase_price'];
                }, 0));
                $totalProductPrice = ceil(collect($sale)->reduce(function ($a, $c) {
                    $currentPrice = $c['product_price'] - ($c['product_price'] * intval($c['discount']) / 100);
                    if ($c['sale']['kaspi_red']) {
                        $currentPrice -= $currentPrice * Sale::KASPI_RED_PERCENT;
                    }
                    if ($c['sale']['balance'] > 0) {
                        $currentPrice  -= $c['balance'];
                    }
                    return $a + $currentPrice;
                }, 0));
                return [
                    'product_id' => $sale[0]['product']['id'],
                    'product_name' => $sale[0]['product']['product']['product_name'],
                    'attributes' => collect($sale[0]['product']['attributes'])->pluck('attribute_value')->merge(collect($sale[0]['product']['product']['attributes'])->pluck('attribute_value'))->join(', '),
                    'manufacturer' => $sale[0]['product']['product']['manufacturer']['manufacturer_name'],
                    'manufacturer_id' => $sale[0]['product']['product']['manufacturer']['id'],
                    'count' => count($sale),
                    'total_purchase_price' => $totalPurchasePrice,
                    'total_product_price' => $totalProductPrice,
                    'margin' => $totalProductPrice > 0 ? $totalProductPrice - $totalPurchasePrice : 0,
                ];
            })->values()->sortBy('product_name');
    }

    private function calculateTotalAmount($sales) {
        $kaspiSales = collect($sales)->filter(function ($i) {
            return $i['payment_type'] === 4;
        })->values();

        $internetSales = collect($sales)->filter(function ($i) {
            return $i['user_id'] === 2;
        })->values();

        $kaspiAndWebSales = $kaspiSales->mergeRecursive($internetSales);
        $otherSales = collect($sales)->diff($kaspiAndWebSales)->all();

        $retailSales = collect($otherSales)
            ->filter(function ($sale) {
                return !$sale['is_opt'];
            })
            ->values()
            ->groupBy('store_id')
            ->map(function ($sale, $store_id) {
                return [
                    'store_id' => $store_id,
                    'amount' => $this->getTotalAmount($sale)
                ];
            });

        $optSales = collect($otherSales)
            ->filter(function ($sale) {
                return $sale['is_opt'];
            })
            ->values();

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

        return $kaspiSales
            ->mergeRecursive($retailSales)
            ->mergeRecursive($internetSales)
            ->mergeRecursive($optSales)
            ->groupBy('store_id')
            ->map(function ($item) {
                return collect($item)->first();
            });
    }

    private function getTotalAmount($sales) {
        return (collect($sales)->reduce(function ($a, $c){
            $price = intval(collect($c['products'])->reduce(function ($_a, $_c) use ($c) {
                $price = $_c['product_price'] - ($_c['product_price'] * ($_c['discount'] / 100));
                return $_a + $price;
            }, 0));
            if ($c['kaspi_red']) {
                $price -= $price * Sale::KASPI_RED_PERCENT;
            }
            return ceil($a + $price - $c['balance']);
        }, 0));
    }

    public function editSaleList(Sale $sale, Request $request, SaleService $saleService) {
        \DB::beginTransaction();
        $saleProducts = $sale->products;
        // 1. Удаляем старый состав заказа и возвращаем на склад
        $saleProducts->each(function ($product) use ($sale) {
            ProductBatch::query()
                ->whereId($product['product_batch_id'])
                ->increment('quantity');
        });
        SaleProduct::query()
            ->whereSaleId($sale->id)
            ->delete();
        // 2. Удаляем клиентскую транзакцию, если она была
        if ($sale->client_id !== -1) {
            ClientTransaction::whereSaleId(($sale->id))->delete();
        }
        // 3. Обновляем поля продажи
        $sale->update([
            'client_id' => $request->get('client_id', -1),
            'discount' => $request->get('discount', 0)
        ]);
        $sale->fresh();
        // 4. Заново создаем список продуктов
        $cart = $request->get('cart');
        //return $request->all();
        $saleService->createSaleProducts($sale, $sale->store_id, $cart);
        $saleService->createClientSale($sale->client_id, $sale->discount, $cart, $sale->balance, $sale->user, $sale->id, $sale->partner_id);
        $sale->fresh();
        \DB::commit();
        return new ReportsResource(Sale::find($sale->id));
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

    public function getMotivationReport(Request $request) {
        $motivations = BrandMotivation::all();
        $motivations = $motivations->map(function ($item) {
            return [
                'name' => Manufacturer::whereIn('id', $item['brands'])
                    ->get()
                    ->pluck('manufacturer_name')
                    ->join(' | ')
                ,
                'motivation' => $this->getBrandsMotivation($item['brands']),
                'amount' => $item['amount']
            ];
        });

        $sellers = User::sellers()
            ->with('store:id,name')
            ->select(['id', 'store_id', 'name', 'role_id'])
            ->get();

        return $sellers->map(function ($seller) use ($motivations) {
            return [
                'id' => $seller['id'],
                'name' => $seller['name'] . ' | ' . $seller['store']['name'],
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
                        'percent' => round(($currentMotivation['sum'] == 0 || $currentPlan == 0) ? 0 : 100 * $currentMotivation['sum'] / $currentPlan)
                    ];
                })
            ];
        })->filter(function ($item) {
            return collect($item['motivations'])->filter(function ($i) {
                    return $i['plan'] > 0;
                })->count() > 0;
        })->values()->all();
    }

    private function getBrandsMotivation($brands) {
        $products_id = Product::whereIn('manufacturer_id', $brands)->select(['id'])->get();
        $products_id = ProductSku::whereIn('product_id', $products_id)->select(['id'])->get()->pluck('id');
        $date_start = now()->startOfMonth();
        $date_finish = now()->endOfMonth();
        return SaleProduct::query()
            ->whereIn('product_id', $products_id)
            ->whereHas('sale', function ($q) use ($date_start, $date_finish) {
                $q->whereDate('created_at', '>=', $date_start)
                    ->whereDate('created_at', '<=', $date_finish)
                    ->physicalSales();
            })
            ->with('sale')->get()->map(function ($sale) {
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

    public function getSaleTypes() {
        return collect(Sale::PAYMENT_TYPES)->map(function ($item, $key) {
            return [
                'id' => $key,
                'name' => collect($item)->first()
            ];
        });
    }

    public function sendTelegramOrderMessage(Sale $sale, TelegramService $telegramService) {
        try {
            $message = $this->getDeliveryMessage($sale);
            $ironDeliveryChat = '-1001615606567';
            $telegramService->sendMessage($ironDeliveryChat, urlencode($message));
            return response()->json([], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 200);
        }
    }

    private function getDeliveryMessage($sale) {
        $sale = new ReportsResource($sale);
        $message = 'Новая доставка №' . $sale->id . "\n";
        $message .= 'ФИО: ' . $sale['client']['client_name'] . "\n";
        $message .= 'Телефон: ' . $sale['client']['client_phone'] . "\n";
        $message .= $sale['comment'] . "\n";
        $message .= $sale['is_paid'] ? 'Оплачен ✅✅✅' : 'НЕ ОПЛАЧЕН ❌❌❌';
        $message .= "\n";
        $message .= 'Способ оплаты: ' . Sale::PAYMENT_TYPES[$sale->payment_type]['name'] . "\n";
        $message .= "Товары: \n";
        $saleProducts = $sale['products'];
        $products = collect($sale['products'])
            ->groupBy('product_id')
            ->map(function ($item) {
                return collect($item)->first();
            })
            ->values()
            ->all();
        foreach ($products as $key => $product) {
            $_product = $product['product']['product'];
            $attributes = collect($product['product']['product']['attributes'])
                ->mergeRecursive(collect($product['product']['attributes']))
                ->pluck('attribute_value')
                ->join(', ');
            $count = $saleProducts->filter(function ($item) use ($product) {
                return $item['product_id'] === $product['product_id'];
            })->count();
            $message .=
                ($key + 1) . '. ' .
                $_product['product_name'] . ' ' . $attributes .
                ' | ' . $count . 'шт' .
                "\n";
        }
        $message .= 'К оплате: ' . $sale->final_price_without_red . 'тнг';
        return $message;
    }
}
