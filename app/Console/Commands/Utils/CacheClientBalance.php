<?php

namespace App\Console\Commands\Utils;

use App\Client;
use Illuminate\Console\Command;

class CacheClientBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Кеширует баланс клиентов';

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
    public function handle()
    {
        Client::query()
            ->with(['sales', 'transactions'])
            ->select(['id', 'client_name'])
            ->get()
            ->each(function (Client $client) {
                $client->update([
                    'cached_balance' => $client->transactions->sum('amount'),
                    'cached_total_sale_amount' => $client->sales->sum('amount')
                ]);
            });
    }
}
