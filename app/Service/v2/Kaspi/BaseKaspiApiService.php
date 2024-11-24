<?php

namespace App\Service\v2\Kaspi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BaseKaspiApiService
{
    protected Client $client;
    protected array $headers;


    public function __construct()
    {
        $cookies = app(KaspiCookiesResolver::class)->resolve();
        $this->headers = [
            'accept' => 'application/json, text/plain, */*',
            'accept-language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'cookie' => $cookies,
            'origin' => 'https://kaspi.kz',
            'priority' => 'u=1, i',
            'referer' => 'https://kaspi.kz/',
            'sec-ch-ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"Windows"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-site',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'x-auth-version' => '3',
        ];
        $this->client = new Client([
            'base_uri' => 'https://mc.shop.kaspi.kz/mc/api/',
            'timeout'  => 10.0, // Таймаут в секундах
        ]);
    }

    /**
     * @throws \Exception
     */
    public function get(string $endpoint, array $queryParams = [])
    {
        $queryParams = [
            'count' => 10,
            'selectedTabs' => 'SIGN_REQUIRED',
            'startIndex' => 0,
            'loadPoints' => 'true',
            '_m' => 'IronAddicts',
        ];
        try {
            $response = $this->client->get($endpoint, [
                'headers' => $this->headers,
                'query' => $queryParams,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Выполняет POST-запрос.
     *
     * @param string $endpoint
     * @param array $queryParams
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function post(string $endpoint, array $queryParams = [], array $data = [])
    {
        try {
            $response = $this->client->post($endpoint, [
                'headers' => $this->headers,
                'query' => $queryParams,
                'json' => $data,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
