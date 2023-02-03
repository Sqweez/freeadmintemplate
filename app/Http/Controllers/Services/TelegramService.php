<?php


namespace App\Http\Controllers\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class TelegramService {

    /**
     * @throws GuzzleException
     */

    private string $token;
    private Client $client;

    public function __construct() {
        $this->token = config('telegram.BOT_TOKEN');
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function sendMessage($chat_id, $message): ResponseInterface {
        return $this->client->request('POST', $this->getURL(), [
            'form_params' => [
                'parse_mode' => 'HTML',
                'chat_id' => $chat_id,
                'text' => urldecode($message)
            ],
        ]);
    }

    private function getURL(): string {
        return sprintf('https://api.telegram.org/bot%s/sendMessage', $this->token);
    }
}
