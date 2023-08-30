<?php

namespace App\Jobs\Notifications;

use App\Actions\Sale\GetSaleDeliveryMessageAction;
use App\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDeliveryNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @param GetSaleDeliveryMessageAction $action
     * @return PendingDispatch
     */
    public function handle(GetSaleDeliveryMessageAction $action): PendingDispatch
    {
        $message = $action->handle($this->sale);
        $chat_id = $this->sale->store->telegram_chat_id ?: config('telegram.DELIVERY_CHAT');
        return SendTelegramMessageJob::dispatch($chat_id, $message);
    }
}
