<?php


namespace App\Http\Controllers\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class TelegramService {

    /**
     * @throws GuzzleException
     */
    public function sendMessage($chat_id, $message): ResponseInterface {
        $client = new Client();
        return $client->request('POST', 'https://api.telegram.org/bot' . env('TELEGRAM_TOKEN') .'/sendMessage', [
            'form_params' => [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => urldecode($message)
            ],
        ]);
    }
}
