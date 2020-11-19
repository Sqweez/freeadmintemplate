<?php

namespace App\Http\Controllers\api;

use App\AnalyticSearch;
use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\PartnerResource;
use App\Product;
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
            ->with('partner_sales', 'partner_sales.products')
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
}
