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
        $clients = $this->getClients();
        $this->line($clients->count());
        $this->line($clients->max('cached_balance') ?? '0');
        $this->backupClientBalances($clients);
        Client::whereIn('id', $clients->pluck('id'))->update([
            'cached_balance' => 0,
        ]);
        return 0;
    }

    private function getClients()
    {
        return Client::query()
            ->where('cached_balance', '>', 0)
            ->whereDoesntHave('sales', function ($query) {
                $query->where('created_at', '>=', today()->subYear());
            })
            ->with(['sales' => function ($q) {
                return $q->latest('created_at');
            }])
            ->get();
    }

    private function backupClientBalances($clients)
    {
        $mappedClients = $clients->map(function (Client $client) {
            $lastPurchasedAt = optional($client->sales->first())->created_at;
            if ($lastPurchasedAt) {
                $lastPurchasedAt = format_datetime($lastPurchasedAt);
            }
            return [
                'id' => $client->id,
                'name' => $client->client_name,
                'balance' => $client->cached_balance,
                'last_purchased_at' => $lastPurchasedAt
            ];
        })->toArray();

        $jsonContent = json_encode($mappedClients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $storagePath = 'json/clients-balance-backup_' . now()->unix() . '.json';
        \Storage::disk('local')->put($storagePath, $jsonContent);
        $this->info('Backup has been created');
    }
}
