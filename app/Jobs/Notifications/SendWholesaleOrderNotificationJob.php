<?php

namespace App\Jobs\Notifications;

use App\Actions\Sale\GetWholeSaleMessageAction;
use App\Http\Services\NotificationService;
use App\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWholesaleOrderNotificationJob implements ShouldQueue
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
    public function handle(NotificationService $notificationService, GetWholeSaleMessageAction $action): void
    {
        $message = $action->handle($this->sale);
        $notificationService->sendNotificationMessage($message);
    }
}
