<?php


namespace App\Http\Controllers\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class TelegramService {

    public function sendMessage($chat_id, $message) {
        $client = new Client();
        return $client->get('https://api.telegram.org/bot' . env('TELEGRAM_TOKEN') .'/sendMessage?parse_mode=HTML&chat_id=' . $chat_id . '&text=' . $message);
    }
}
