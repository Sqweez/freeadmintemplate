<?php

namespace App\Console\Commands\Clients;

use App\Client;
use Illuminate\Console\Command;

class FlushInactiveClientsBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:flush-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает балансы клиентов, у которых не было покупок более года';

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
        $clients = Client::query()
            ->where('cached_balance', '>', 0)
            ->whereDoesntHave('purchases', function ($query) {
                return $query->where('created_at', '>=', today()->subYear());
            })
            ->with('purchases')
            ->get();

        $this->line($clients->count());

        return 0;
    }
}
