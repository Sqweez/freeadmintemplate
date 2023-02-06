<?php

namespace App\Jobs\Notifications\Order;

use App\Actions\Order\CreateOrderMessageAction;
use App\Http\Controllers\Services\TelegramService;
use App\Http\Services\NotificationService;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Order $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void|null
     */
    public function handle(CreateOrderMessageAction $action, TelegramService $service)
    {
        $message = $action->handle($this->order);
        return $service->send(config('telegram.TELEGRAM_KZ_CHAT_ID'), $message);
    }
}
