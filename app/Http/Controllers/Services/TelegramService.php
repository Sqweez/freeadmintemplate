<?php


namespace App\Http\Controllers\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class TelegramService {

    private $token;

    public function __construct() {
        $this->token = '1104581090:AAHx5aikkFmKV14oO5FLQgvWwozxKmPa_Dw';
    }

    public function sendMessage($chat_id, $message) {
        $client = new Client();
        return $client->get('https://api.telegram.org/bot' . $this->token .'/sendMessage?chat_id=' . $chat_id . '&text=' . $message);
    }

}
