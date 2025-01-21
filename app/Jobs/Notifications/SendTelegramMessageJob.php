<?php

namespace App\Jobs\Notifications;

use App\Http\Controllers\Services\TelegramService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    private string $message;
    private string $chat_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $chat_id, string $message)
    {
        $this->chat_id = $chat_id;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @param TelegramService $service
     * @return void
     * @throws GuzzleException
     */
    public function handle(TelegramService $service): void
    {
        $service->sendMessage($this->chat_id, $this->message);
    }
}
