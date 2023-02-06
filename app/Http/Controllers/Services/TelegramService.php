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
    public function sendMessage($chat_id, $message) {
        $messages = is_array($message) ? $message : \Arr::wrap($message);
        foreach ($messages as $_message) {
            $this->client->request('POST', $this->getURL(), [
                'form_params' => [
                    'parse_mode' => 'HTML',
                    'chat_id' => $chat_id,
                    'text' => $this->getMessage($_message)
                ],
            ]);
        }
    }

    private function getURL(): string {
        return sprintf('https://api.telegram.org/bot%s/sendMessage', $this->token);
    }

    public function send($chat_id, $message) {
        return $this->sendMessage($chat_id, $message);
    }

    private function getMessage($message) {
        if (preg_match("@^[a-zA-Z0-9%+-_]*$@", $message)) {
            return urldecode($message);
        } else {
            return $message;
        }
    }
}
