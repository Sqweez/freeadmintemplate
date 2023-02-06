<?php

namespace App\Http\Services;

use App\Http\Controllers\Services\TelegramService;
use App\Jobs\Notifications\SendTelegramMessageJob;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Bus\PendingDispatch;
use Psr\Http\Message\ResponseInterface;

class NotificationService {

    /**
     */
    public function sendNotificationMessage ($message): PendingDispatch {
        $chat_id = config('telegram.NOTIFICATIONS_CHAT');
        return SendTelegramMessageJob::dispatch($chat_id, $message);
    }

    public function sendSaleMessage() {

    }

    public function sendBirthdayMessage() {

    }

    public function sendSaleDeliveryMessage($message): PendingDispatch {
        $chat_id = config('telegram.DELIVERY_CHAT');
        return SendTelegramMessageJob::dispatch($chat_id, $message);
    }

    /**
     * @throws GuzzleException
     */
    public function send($message, $chat_id): ResponseInterface {
        $telegramService = new TelegramService();
        return $telegramService->sendMessage($chat_id, $message);
    }
}
