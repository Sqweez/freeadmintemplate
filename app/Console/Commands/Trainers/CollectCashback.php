<?php

namespace App\Console\Commands\Trainers;

use App\ClientTransaction;
use App\Http\Controllers\Services\TelegramService;
use App\Sale;
use App\Store;
use Illuminate\Console\Command;

class CollectCashback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:trainer-cashback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // @TODO создать чат
        $chat_ids = [
            22 => '-4053634757',
            23 => '-4033835182'
        ];

        foreach ($chat_ids as $key => $chat_id) {
            $sales = Sale::query()
                ->whereDate('created_at', '>=', today()->subDays(7)->startOfDay())
                ->where('store_id', $key)
                ->get();

            $transactions = ClientTransaction::query()
                ->whereIn('sale_id', $sales->pluck('id'))
                ->where('type_id', ClientTransaction::TYPE_PARTNER_ROYALTY)
                ->with('client')
                ->get()
                ->groupBy('client_id')
                ->map(function ($items, $key) {
                    return [
                        'client_id' => $key,
                        'amount' => collect($items)->reduce(function ($a, $c) {
                            return $a + $c['amount'];
                        }, 0),
                        'client' => collect($items)->first()['client']
                    ];
                })
                ->values();

            $message = $this->buildMessage($transactions, $key);
            (new TelegramService())->send($chat_id, $message);
        }

        return 1;
    }

    private function buildMessage($transactions, $store_id): string {
        $store = Store::find($store_id);
        $messages = [];
        $messages[] = sprintf('Заработок тренеров с продаж за текущую неделю (%s)', $store->name);
        foreach ($transactions as $key => $transaction) {
            $message = sprintf("%s. %s: %s тенге", ($key + 1), $transaction['client']['client_name'], number_format($transaction['amount']));
            $messages[] = $message;
        }

        return collect($messages)->join("\n");
    }
}
