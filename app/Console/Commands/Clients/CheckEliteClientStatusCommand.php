<?php

namespace App\Console\Commands\Clients;

use App\Client;
use App\v2\Models\Loyalty;
use Illuminate\Console\Command;

class CheckEliteClientStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elite:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет условия и отключает ненужных платиновых клиентов';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        Client::query()
            ->whereLoyaltyId(2)
            ->cursor()
            ->each(function (Client $client) {
                $this->line($client->client_name);
                $totalSalesSum =  $client
                    ->real_sales()
                    ->whereDate('created_at', '>=', now()->subMonth()->startOfMonth())
                    ->whereDate('created_at', '<=', now()->subMonth()->endOfMonth())
                    ->get()
                    ->sum('final_price');
                if ($totalSalesSum < Loyalty::MONTHLY_ELITE_SUM_THRESHOLD) {
                    $client->update([
                        'loyalty_id' => 1
                    ]);
                }
            });

        return 0;
    }
}
