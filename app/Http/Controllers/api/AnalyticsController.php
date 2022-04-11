<?php

namespace App\Http\Controllers\api;

use App\AnalyticSearch;
use App\Arrival;
use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\SaleService;
use App\Http\Resources\shop\PartnerResource;
use App\Product;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function storeSearch(Request $request) {
        $user_token = $request->get('user_token');
        $search = $request->get('search');

        $client = Client::ofToken($user_token)->first();

        $client_id = $client ? $client['id'] : -1;

        return AnalyticSearch::create([
            'search' => $search,
            'client_id' => $client_id
        ]);
    }

    public function getSalesSchedule(Request $request) {
        $startDate = Carbon::parse($request->get('date'))->startOfMonth()->locale('ru');
        $finishDate = Carbon::parse($request->get('date'))->endOfMonth()->locale('ru');
        $daysInMonth = $startDate->daysInMonth;
        $dateArray = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = $i;
            $dateArray[] = $day;
        }
        return Sale::query()
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $finishDate)
            ->with(['store', 'products'])
            ->get()
            ->groupBy('store_id')
            ->map(function ($sale, $store_id) use ($dateArray) {
                return collect($dateArray)->map(function ($date) use ($sale) {
                    $needleSales = collect($sale)->filter(function ($i) use ($date) {
                        return Carbon::parse($i['created_at'])->day === $date;
                    })->values();
                    return (new SaleService())->calculateSaleFinalAmount($needleSales);
                });
            });
    }

    public function partners(Request $request) {
        $partners = collect(Client::Partner()->with('city')->with('transactions')->get())->map(function ($client) {
            $client['balance'] = collect($client['transactions'])->reduce(function ($i, $a) {
                return $a['amount'] + $i;
            }, 0);
            unset($client['transactions']);
            $client['city'] = $client['city']['city'];
            return $client->only(['id', 'client_name', 'balance', 'city', 'client_phone']);
        });
        return $partners;
    }

    public function partnerStats($id) {
        $partner = collect(Client::where('id', $id)->with('partner_sales', 'partner_sales.products')->first());
        $partner_sales = collect($partner->only('partner_sales')->collapse());
        $daily_sales = $partner_sales->filter(function ($i) {
            return Carbon::parse($i['created_at'])->format('yy-m-d') === now()->format('yy-m-d');
        });
        $weekly_sales = $partner_sales->filter(function ($i) {
            $date = Carbon::parse($i['created_at'])->format('yy-m-d');
            $now = now()->format('yy-m-d');
            $weekAgo = now()->subDays(7)->format('yy-m-d');
            return $date >= $weekAgo && $date <= $now;
        });
        $monthly_sales = $partner_sales->filter(function ($i) {
            $date = Carbon::parse($i['created_at'])->format('yy-m-d');
            $now = now()->format('yy-m-d');
            $monthAgo = now()->subDays(30)->format('yy-m-d');
            return $date >= $monthAgo && $date <= $now;
        });
        return [
            'daily' => $this->getPartnerArray($daily_sales),
            'weekly' => $this->getPartnerArray($weekly_sales),
            'monthly' => $this->getPartnerArray($monthly_sales),
            'all_time' => $this->getPartnerArray($partner_sales),
        ];
    }

    private function getTotalSaleSum($sales) {
        return collect($sales)->reduce(function ($a, $c) {
            $product_total = collect($c['products'])->reduce(function ($_a, $_c) {
                return $_a + $_c['product_price'];
            }, 0);
            return ($product_total - $product_total * ($c['discount'] / 100)) + $a;
        }, 0);
    }

    private function getUniqueClientsCount($sales) {
        return collect($sales)->pluck('client_id')->unique()->values()->filter(function ($i) {
            return $i !== -1;
        });
    }

    private function getPartnerArray($sales) {
        if (!count($sales)) {
            return [];
        }
        return [
            'count' => count($sales),
            'total_sales_sum' => $this->getTotalSaleSum($sales),
            'avg_sum' => $this->getAverageSum($sales),
            'most_popular_products' => $this->getMostPopularProduct($sales),
            'unique_clients_count' => count($this->getUniqueClientsCount($sales)),
            'unique_clients' => $this->getClients($this->getUniqueClientsCount($sales))
        ];
    }

    private function getClients($clients) {
        return Client::find($clients);
    }

    private function getAverageSum($sales) {
        try {
            return $this->getTotalSaleSum($sales) / count($sales);
        } catch (\Exception $exception) {
            return 0;
        }
    }

    private function getMostPopularProduct($sales) {
        try {
            return collect(collect($sales)->map(function ($i) {
                return $i['products'];
            })
                ->collapse()
                ->groupBy('product_id')
                ->map(function ($i) {
                    return [
                        'product_id' => $i[0]['product_id'],
                        'count' => count($i)
                    ];
                })
                ->values()->all())->sortByDesc('count')
                ->values()->chunk(3)->first()->map(function ($i) {
                    $i['product'] = Product::find($i['product_id']);
                    return $i;
                });
        } catch (\Exception $exception) {
            return [];
        }
    }

    public function getPartnerSales(Request $request) {
        $user_token = $request->get('user_token');
        if (!$user_token) {
            return response()->json(['error' => 'Не передан токен партнера']);
        }
        $client = Client::ofToken($user_token)
            ->with('partner_sales', 'partner_sales.products', 'partner_sales.products.product', 'partner_sales.products.product.product', 'promocodes:client_id,promocode,is_active')
            ->first();

        if (!$client) {
            return response()->json(['error' => 'Клиент не найден']);
        }

        if (!$client->is_partner) {
            return response()->json(['error' => 'Клиент не является партнером']);
        }


        $client['balance'] = $client->balance;
        $client['clients'] = $this->getUniqueClientsCount($client->partner_sales);

        return new PartnerResource($client);
    }

    public function getBrandSales(Request $request) {
        $store_id = $request->get('store_id');
        $date_start = $request->get('date_start');
        $date_finish = $request->get('date_finish');

        $saleQuery = Sale::query();

        if ($store_id != -1) {
            $saleQuery = $saleQuery->whereStoreId($store_id);
        }

        $sales = $saleQuery
            ->whereDate('created_at', '>=', $date_start)
            ->whereDate('created_at', '<=', $date_finish)
            ->with(['products', 'products.product', 'products.product.product:product_name,manufacturer_id,id', 'products.product.product.manufacturer'])
            ->get()
            ->pluck('products')
            ->map(function ($item) {
                return collect($item)->map(function ($sale) {
                    $sale['manufacturer'] = $sale['product']['product']['manufacturer']['manufacturer_name'] ?? 'Неизвестно';
                    unset($sale['product']);
                    return $sale;
                })->filter(function ($sale) {
                    return $sale['manufacturer'] !== 'Неизвестно';
                })->values();
            })->filter(function ($item) {
                return count($item) > 0;
            })->values()->all();

        $salesMerged = [];
        foreach ($sales as $sale) {
            foreach ($sale as $item) {
                $salesMerged[] = $item;
            }
        }

        return collect($salesMerged)
            ->groupBy('manufacturer')
            ->map(function ($item, $key) {
                return [
                    'manufacturer' => $key,
                    'total' => collect($item)->reduce(function ($a, $c) {
                        return $a + $c['product_price'] - ($c['product_price'] * $c['discount'] / 100);
                    }, 0)
                ];
            })
            ->values()
            ->filter(function ($item) {
                return $item['total'] > 0;
            })
            ->values()
            ->sortByDesc('total')
            ->values()
            ->all();
    }

    public function getTopPartners(Request $request) {
        $top10Trainers = Sale::query()
            ->where('partner_id', '!=', null)
            ->where('partner_id', '!=', 0)
            ->when($request->has('date'), function ($q) use ($request) {
                return $q
                ->whereDate('created_at', '>=', Carbon::parse($request->get('date'))->startOfMonth())
                ->whereDate('created_at', '<=', Carbon::parse($request->get('date'))->endOfMonth());
            })
            ->when(!$request->has('date'), function ($q) {
                return $q->whereDate('created_at', '>=', now()->startOfMonth())
                    ->whereDate('created_at', '<=', now()->endOfMonth());
            })
            ->with(['products' => function ($query) {
                return $query->select(['product_price', 'discount', 'sale_id']);
            }])
            ->select(['id', 'partner_id'])
            ->get()
            ->groupBy('partner_id')
            ->map(function ($item, $key) {
                return [
                    'partner_id' => $key,
                    'amount' => collect($item)->reduce(function ($a, $c) {
                        return $a + ceil(collect($c['products'])->reduce(function ($_a, $_c) {
                                $amount = $_c['product_price'] - ($_c['product_price'] * ($_c['discount'] / 100));
                                return $_a + $amount;
                            }, 0));
                    }, 0)
                ];
            })
            ->values()
            ->sortByDesc('amount')
            ->take(12)
            ->values()
            ->map(function ($item) {
                $client = Client::find($item['partner_id']);
                if (!$client) {
                    return null;
                }
                $clientNameArray = explode(' ', $client->client_name);
                $clientFirstName = $clientNameArray[0];
                array_shift($clientNameArray);
                $clientLastName = implode(' ', $clientNameArray);
                return [
                    'amount' => $item['amount'],
                    'first_name' => $clientFirstName,
                    'last_name' => $clientLastName,
                    'name' => $client->client_name,
                    'trainer_job' => $client->job,
                    'trainer_image' => url('/') . ($client->photo ? \Storage::url($client->photo) : \Storage::url('partners/partner_default.jpg')),
                    'trainer_instagram' => $client->instagram
                ];
            })->filter(function ($item) {
                return !is_null($item);
            })
            ->values()
            ->take(10);

        $trainersCount = $top10Trainers->count();
        $difference = 10 - $trainersCount;
        if ($difference > 0) {
            $trainers = Client::partner()->take($difference)->get();
            $trainers = $trainers->map(function ($item) {
                $clientNameArray = explode(' ', $item['client_name']);
                $clientFirstName = $clientNameArray[0];
                array_shift($clientNameArray);
                $clientLastName = implode(' ', $clientNameArray);
                return [
                    'amount' => 0,
                    'first_name' => $clientFirstName,
                    'last_name' => $clientLastName,
                    'trainer_job' => $item['job'],
                    'name' => $item['client_name'],
                    'trainer_image' => url('/') . ($item['photo'] ? \Storage::url($item['photo']) : \Storage::url('partners/partner_default.jpg')),
                    'trainer_instagram' => $item['instagram']
                ];
            });
            $top10Trainers = $top10Trainers->mergeRecursive($trainers);
        }

        $top1Trainer = Sale::query()
            ->where('partner_id', '!=', null)
            ->where('partner_id', '!=', 0)
            ->whereDate('created_at', '>=', now()->subMonth()->startOfMonth())
            ->whereDate('created_at', '<=', now()->subMonth()->endOfMonth())
            ->with(['products' => function ($query) {
                return $query->select(['product_price', 'discount', 'sale_id']);
            }])
            ->select(['id', 'partner_id'])
            ->get()
            ->groupBy('partner_id')
            ->map(function ($item, $key) {
                return [
                    'partner_id' => $key,
                    'amount' => collect($item)->reduce(function ($a, $c) {
                        return $a + ceil(collect($c['products'])->reduce(function ($_a, $_c) {
                                $amount = $_c['product_price'] - ($_c['product_price'] * ($_c['discount'] / 100));
                                return $_a + $amount;
                            }, 0));
                    }, 0)
                ];
            })
            ->values()
            ->sortByDesc('amount')
            ->take(1)
            ->values()
            ->map(function ($item) {
                $client = Client::find($item['partner_id']);
                if (!$client) {
                    return null;
                }
                $clientNameArray = explode(' ', $client->client_name);
                $clientFirstName = $clientNameArray[0];
                array_shift($clientNameArray);
                $clientLastName = implode(' ', $clientNameArray);
                return [
                    'amount' => $item['amount'],
                    'first_name' => $clientFirstName,
                    'last_name' => $clientLastName,
                    'trainer_job' => $client->job,
                    'trainer_image' => url('/') . ($client->photo ? \Storage::url($client->photo) : \Storage::url('partners/partner_default.jpg')),
                    'trainer_instagram' => $client->instagram
                ];
            })->filter(function ($item) {
                return !is_null($item);
            })
            ->first();
        if (!$top1Trainer) {
            $top1Trainer = Client::partner()->take(1)->get();
            $top1Trainer = $top1Trainer->map(function ($item) {
                $clientNameArray = explode(' ', $item['client_name']);
                $clientFirstName = $clientNameArray[0];
                array_shift($clientNameArray);
                $clientLastName = implode(' ', $clientNameArray);
                return [
                    'amount' => 0,
                    'first_name' => $clientFirstName,
                    'last_name' => $clientLastName,
                    'trainer_job' => $item['job'],
                    'trainer_image' => url('/') . ($item['photo'] ? \Storage::url($item['photo']) : \Storage::url('partners/partner_default.jpg')),
                    'trainer_instagram' => $item['instagram']
                ];
            })->first();
        }
        return [
            'top1' => $top1Trainer,
            'top10' => $top10Trainers
        ];
    }

    public function getSaleSellersAnalytics(Request $request, SaleService $saleService) {
        $startDate = Carbon::parse($request->get('start'))->startOfMonth()->locale('ru');
        $finishDate = Carbon::parse($request->get('finish'))->endOfMonth()->locale('ru');
        $products = $request->get('products', null);
        $sales = Sale::query()
            ->with('products')
            ->with('products.product.attributes')
            ->with('products.product.product')
            ->with('user:id,name')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $finishDate)
            ->select(['id', 'kaspi_red', 'created_at', 'balance', 'user_id'])
            ->get();

        if ($products && count($products) > 0) {
            $sales = $sales->map(function ($sale) use ($products) {
                $sale_products = collect($sale['products'])->filter(function ($product) use ($products) {
                    return in_array($product['product_id'], $products);
                });
                unset($sale['products']);
                $sale['products'] = $sale_products;
                return $sale;
            })->filter(function ($sale) {
                return count($sale['products']) > 0;
            })->values();
        }

        return $sales->groupBy('user_id')
            ->map(function ($sale, $key) use ($saleService) {
                return [
                    'amount' => $saleService->calculateSaleFinalAmount($sale),
                    'user' => $sale[0]['user'],
                    'products' => $this->getProductsList($sale)
                ];
            })
            ->values()->all();
    }

    private function getProductsList($sale): array {
        $products = [];
        foreach ($sale as $item) {
            foreach ($item['products'] as $product) {
                $productsIds = array_map(function ($i) {
                    return $i['product_id'];
                }, $products);
                $key = array_search($product['product_id'], $productsIds);
                if ($key !== false) {
                    $products[$key]['count'] += 1;
                    $products[$key]['product_price'] += $product['product_price'] * ((100 - $product['discount']) / 100);
                } else {
                    $products[] = [
                        'product_id' => $product['product_id'],
                        'count' => 1,
                        'product_name' => $product['product']['product']['product_name'],
                        'product_price' => $product['product_price'] * ((100 - $product['discount']) / 100),
                        'attributes' => collect($product['product']['attributes'])->pluck('attribute_value')->join(' '),
                    ];
                }
            }
        }
        return $products;
    }

    public function getSaleAnalytics(Request $request, SaleService $saleService) {
        $startDate = Carbon::parse($request->get('start'))->startOfMonth()->locale('ru');
        $finishDate = Carbon::parse($request->get('finish'))->endOfMonth()->locale('ru');
        $products = $request->get('products', null);
        $sales = Sale::query()
            ->with('products')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $finishDate)
            ->select(['id', 'kaspi_red', 'created_at', 'balance'])
            ->get();

        if ($products && count($products) > 0) {
            $sales = $sales->map(function ($sale) use ($products) {
                $sale_products = collect($sale['products'])->filter(function ($product) use ($products) {
                    return in_array($product['product_id'], $products);
                });
                unset($sale['products']);
                $sale['products'] = $sale_products;
                return $sale;
            })->filter(function ($sale) {
                return count($sale['products']) > 0;
            })->values();
        }

        $saleAnalytics =  $sales->groupBy(function ($i) {
            return Carbon::parse($i['created_at'])->format('Y-m');
        })->map(function ($sale, $key) use ($saleService) {
            $date = explode('-', $key);
            $year = $date[0];
            $monthNameRu = ucfirstRu(Carbon::parse($key)->locale('ru')->getTranslatedMonthName());
            return [
                'amount' => $saleService->calculateSaleFinalAmount($sale),
                'period' => $key,
                'date_name' => "$monthNameRu, $year"
            ];
        })->values()->all();
        return $this->formatArrayOutput($startDate, $finishDate, $saleAnalytics);
    }

    public function getArrivalAnalytics(Request $request) {
        $startDate = Carbon::parse($request->get('start'))->startOfMonth()->locale('ru');
        $finishDate = Carbon::parse($request->get('finish'))->endOfMonth()->locale('ru');
        $arrivals = Arrival::query()
            ->where('is_completed', true)
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $finishDate)
            ->with(['products.product.product' => function ($q) {
                return $q->select('id', 'product_price');
            }])
            ->select(['id', 'updated_at'])
            ->get();

        $arrivalAnalytics = $arrivals->groupBy(function ($item) {
            return Carbon::parse($item['updated_at'])->format('Y-m');
        })->map(function ($item, $key) {
            $date = explode('-', $key);
            $year = $date[0];
            $monthNameRu = ucfirstRu(Carbon::parse($key)->locale('ru')->getTranslatedMonthName());
            return [
                'amount' => collect($item)->reduce(function ($a, $c) {
                    return $a + collect($c['products'])->reduce(function ($_a, $_c) {
                        $amount = !is_null($_c['product']) ? $_c['count'] * $_c['product']['product']['product_price'] : 0;
                        return $_a + $amount;
                        }, 0);
                }, 0),
                'period' => $key,
                'date_name' => "$monthNameRu, $year"
            ];
        })->values()->all();
        return $this->formatArrayOutput($startDate, $finishDate, $arrivalAnalytics);
    }

    private function formatArrayOutput($startDate, $finishDate,  $saleAnalytics) {
        $date = $startDate->clone()->subMonth();
        $diffInMonths = $startDate->diffInMonths($finishDate) + 1;
        $periodArray = [];
        for ($i = 0; $i < $diffInMonths; $i++) {
            $periodArray[] = $date->addMonth()->format('Y-m');
        }
        foreach ($periodArray as $key => $item) {
            if (isset($saleAnalytics[$key]) && $saleAnalytics[$key]['period'] !== $item) {
                $date = explode('-', $item);
                $year = $date[0];
                $monthNameRu = ucfirstRu(Carbon::parse($item)->locale('ru')->getTranslatedMonthName());
                array_splice($saleAnalytics, $key, 0, [[
                    'amount' => 0,
                    'period' => $item,
                    'date_name' => "$monthNameRu, $year"
                ]]);
            }
            if (!isset($saleAnalytics[$key])) {
                $date = explode('-', $item);
                $year = $date[0];
                $monthNameRu = ucfirstRu(Carbon::parse($item)->locale('ru')->getTranslatedMonthName());
                array_push($saleAnalytics, [
                    'amount' => 0,
                    'period' => $item,
                    'date_name' => "$monthNameRu, $year"
                ]);
            }
        }
        return $saleAnalytics;
    }

    public function getClientPartnerSales(Request $request) {
        $dateStart = Carbon::parse($request->get('date', now()))->startOfMonth();
        $dateFinish = Carbon::parse($request->get('date', now()))->endOfMonth();
        $clients = Client::whereLoyaltyId(3)->get();
        $sales = Sale::query()
            ->report()
            ->reportDate([$dateStart, $dateFinish])
            ->whereIn('client_id', $clients->pluck('id'))
            ->get();

        $sales = $sales->groupBy('client_id')->map(function ($client, $key) {
            $data = collect($client);

            $amount = $data->reduce(function ($a, $c) {
                return $a + $c['final_price'];
            }, 0);

            $products = $data
                ->pluck('products')
                ->flatten()
                ->groupBy('product_id')
                ->map(function ($_product, $key) {
                    $products = collect($_product);
                    $product = $products->first()['product'];
                    return [
                        'count' => $products->count(),
                        'product_id' => $key,
                        'product_name' => $product['product_name'],
                        'manufacturer' => $product['manufacturer']['manufacturer_name'],
                        'attributes' => collect($product['attributes'])
                            ->map(function ($attribute) {
                                return $attribute->attribute_value;
                            })
                            ->merge(collect(collect($product['product']['attributes'])
                                ->map(function ($attribute) {
                                    return $attribute->attribute_value;
                                }))),

                    ];
                })->values()->sortByDesc('count')->values()
            ;

            $margin = $amount - $data->reduce(function ($a, $c) {
                    return $a + collect($c['products'])->reduce(function ($_a, $_c) {
                            return $_a + $_c['purchase_price'];
                        }, 0);
                }, 0);
            return [
                'name' => $client[0]['client']['client_name'],
                'amount' => $amount,
                'margin' => $margin,
                'products' => $products
            ];
        });

        return $sales->values()->sortByDesc('amount')->values();
    }

    public function getInactiveTrainers(Request $request) {
        $start = Carbon::parse($request->get('start'));
        $finish = Carbon::parse($request->get('finish'));
        $clients =  Client::query()
            ->where('is_partner', 1)
            ->with('city')
            ->select(['client_name', 'client_phone', 'id', 'is_partner', 'client_city'])
            ->get();

        $ownSales = Sale::query()
            ->with('products')
            ->whereIn('client_id', $clients->pluck('id'))
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->select(['id', 'created_at', 'client_id'])
            ->get();

        $partnerSales = Sale::query()
            ->with('products')
            ->whereIn('partner_id', $clients->pluck('id'))
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->select(['id', 'created_at', 'partner_id'])
            ->get();

        return $clients->map(function ($client) use ($ownSales, $partnerSales){
            $_ownSales = $ownSales->where('client_id', $client['id']);
            $_partnerSales = $partnerSales->where('partner_id', $client['id']);
            $client['own_sales'] = $_ownSales->reduce(function ($a, $c) {
                return $a + collect($c['products'])->reduce(function ($_a, $_c) {
                    return $_a + $_c['product_price'] * (1 - $_c['discount'] / 100);
                    }, 0);
            }, 0);
            $client['partner_sales'] = $_partnerSales->reduce(function ($a, $c) {
                return $a + collect($c['products'])->reduce(function ($_a, $_c) {
                        return $_a + $_c['product_price'] * (1 - $_c['discount'] / 100);
                    }, 0);
            }, 0);
            $client['without_own_sales'] = $_ownSales->count() === 0;
            $client['without_partner_sales'] = $_partnerSales->count() === 0;
            $client['total'] = $client['own_sales'] + $client['partner_sales'];
            return $client;
        })->filter(function ($client) {
            return $client['without_own_sales'] || $client['without_partner_sales'];
        })->values()->sortByDesc('sum')->values();
    }
}
