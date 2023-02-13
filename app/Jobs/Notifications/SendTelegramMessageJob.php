<?php

namespace App\Jobs\Notifications;

use App\Http\Controllers\Services\TelegramService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psr\Http\Message\ResponseInterface;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

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
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function handle(TelegramService $service): ResponseInterface {
        return $service->sendMessage($this->chat_id, $this->message);
    }
}
