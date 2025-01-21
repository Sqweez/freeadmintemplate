<?php

namespace App\Jobs\Sale;

use App\Client;
use App\Http\Controllers\Services\TelegramService;
use App\Sale;
use App\v2\Models\Loyalty;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckEliteClubSaleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Sale $sale;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $telegramService = new TelegramService();
        $this->sale->load('client');
        $client = $this->sale->client;
        if ($client->id === -1 || $client->loyalty_id !== 1) {
            return;
        }
        $currentMonthSalesSum = $client->real_sales()->whereDate(
            'created_at', '>', now()->startOfMonth())
            ->get()
            ->sum('final_price');
        if ($currentMonthSalesSum >= Loyalty::MONTHLY_ELITE_SUM_THRESHOLD) {
            $client->update([
                'loyalty_id' => __hardcoded(2)
            ]);
            $message = $this->getMessage($client);
            $chatId = config('telegram.TELEGRAM_ELITE_CHAT_ID');
            if ($chatId) {
                $telegramService->send($chatId, $message);
            }
        }
    }

    private function getMessage(Client $client): string
    {
        return sprintf(
            "У нас новый участник клуба! \nФИО: %s \nНомер: %s \nДата первой покупки: %s",
            $client->client_name,
            $client->client_phone,
            Carbon::parse(
                $client->real_sales()->first()->created_at
            )->format('d.m.Y')
        );
    }
}
