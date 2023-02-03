<?php

namespace App\Http\Services;

use App\Jobs\Notifications\SendTelegramMessageJob;
use Illuminate\Foundation\Bus\PendingDispatch;

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
}
