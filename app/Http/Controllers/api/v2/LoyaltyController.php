<?php

namespace App\Http\Controllers\api\v2;

use App\Client;
use App\Http\Controllers\Controller;
use App\v2\Models\Loyalty;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index() {
        return Loyalty::all();
    }

    public function checkLoyalties(Request $request) {
        $clients =  Client::platinumClients()
            ->with(['sales' => function ($query) {
                return $query
                    ->whereDate('created_at', '>=', now()->startOfMonth())
                    ->whereDate('created_at', '<=', now()->endOfMonth());
            }])
            ->get()
            ->map(function ($client) {
                $client['sale_amount'] = collect($client['sales'])->reduce(function ($a, $c) {
                    return $a + $c['amount'];
                }, 0);
                unset($client['sales']);
                return $client;
            })->filter(function ($client) {
                return $client['sale_amount'] < Client::PLATINUM_CLIENT_MONTHLY_THRESHOLD;
            })->pluck('id');

        Client::whereIn('id', $clients)->update([
            'loyalty_id' => 1,
            'client_discount' => 10
        ]);
        return $clients;
    }
}
