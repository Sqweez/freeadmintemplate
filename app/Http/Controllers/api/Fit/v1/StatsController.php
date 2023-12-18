<?php

namespace App\Http\Controllers\api\Fit\v1;

use App\Http\Controllers\Controller;
use App\Models\FitClient;
use App\Models\FitServiceSale;
use App\Models\FitTransaction;

class StatsController extends Controller
{
    public function getDashboardStats(): array
    {
        $gymId = auth()->user()->gym_id;
        $transactions = FitTransaction::query()
            ->whereDate('created_at', '>=', today()->startOfDay())
            ->whereDate('created_at', '<=', today()->endOfDay())
            ->where('gym_id', $gymId)
            ->get();

        $revenueItems = $transactions->where('amount', '>', 0);
        $totalClients = FitClient::query()
            ->where('gym_id', $gymId)
            ->count();

        $activeClients = FitServiceSale::query()
            ->where('gym_id', $gymId)
            ->where(function ($query) {
                return $query
                    ->whereNull('valid_until')
                    ->orWhereDate('valid_until', '>=', today());
            })
            ->distinct('client_id')
            ->count();

        return [
            'total_revenue' => $revenueItems->sum('amount'),
            'cashless_revenue' => $revenueItems->where('type', 2)->sum('amount'),
            'cash_revenue' => $revenueItems->where('type', 1)->sum('amount'),
            'withdrawals' => $transactions->where('amount', '<', 0)->sum('amount'),
            'clients_total' => $totalClients,
            'active_clients' => $activeClients,
        ];
    }
}
