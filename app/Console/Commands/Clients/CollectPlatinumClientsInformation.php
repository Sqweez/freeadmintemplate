<?php

namespace App\Console\Commands\Clients;

use App\Client;
use App\Http\Controllers\Services\TelegramService;
use App\Http\Resources\ClientResource;
use App\Http\Services\NotificationService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class CollectPlatinumClientsInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platinum:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Собираем информацию о платиновых клиентах';

    protected int $chunkSize = 30;

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
     * @throws GuzzleException
     */
    public function handle(TelegramService $notificationService)
    {
        $clients = ClientResource::collection(
            Client::with(['sales', 'transactions', 'city', 'loyalty'])
                ->platinumClients()
                ->get()

        )->toArray(\request());

        $clients = collect($clients)->filter(function ($client) { return $client['until_platinum'] > 0; })->values();
        $chunkedClients = $clients->chunk($this->chunkSize);
        $messages = [];
        $chunkedClients->each(function ($chunk, $chunkKey) use (&$messages) {
            $message = "Платиновые клиенты, у которых не хватает покупок:" . "\n";
            $clientKey = 0;
            $message .= collect($chunk)->reduce(function ($a, $c) use ($chunkKey, &$clientKey) {
                $clientKey++;
                return $a . $this->getPlatinumRemainderMessage($c, $clientKey, $chunkKey);
            });
            $messages[] = $message;
        });
        $chatId = config('telegram.BIRTHDAY_CHAT');
        $notificationService->sendMessage($chatId, $messages);
        return 1;
    }

    private function getPlatinumRemainderMessage($client, $clientKey, $chunkKey): string {
        $key = ($this->chunkSize * $chunkKey) + ($clientKey);
        $message = sprintf("%s. %s", ($key), $client['client_name']);
        $message .= "\n";
        $message .= "Телефон: " . $client['client_phone'] . "\n";
        $message .= "Город: " . $client['city'] . "\n";
        $message .= "Остаток:" . $client['until_platinum'] . "₸" . "\n";
        $message .= "<a href='https://api.whatsapp.com/send?phone=" . $client['client_phone'] . "'>Открыть Whatsapp</a>" . "\n";
        return $message;
    }
}
