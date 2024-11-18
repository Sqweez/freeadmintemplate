<?php

namespace App\Service\Kaspi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class BaseKaspiApiService
{

    protected const DEFAULT_PAGE_SIZE = 10;
    private const HEADERS = [
        'Accept' => 'application/vnd.api+json',
        'Content-Type' => 'application/vnd.api+json',
    ];
    private ?string $kaspiToken;
    private ?string $kaspiBaseURL;

    private Client $client;

    public function __construct() {
        $this->kaspiToken = config('kaspi.KASPI_API_KEY');
        $this->kaspiBaseURL = config('kaspi.KASPI_API_BASE_URL');
        $this->client = $this->buildClient();
    }

    private function buildClient(): Client
    {
        return new Client([
            'base_uri' => $this->kaspiBaseURL,
            'headers' => array_merge(self::HEADERS, [
                'X-Auth-Token' => $this->kaspiToken,
            ]),
        ]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    protected function get(string $endpoint, array $queryParams = []): ?array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986),
            ]);
            return $this->handleResponse($response, $queryParams);
        } catch (GuzzleException $e) {
            return $this->handleException($e);
        }
    }

    // Метод для выполнения POST-запроса
    protected function post(string $endpoint, array $data = []): ?array
    {
        try {
            $response = $this->client->post($endpoint, [
                'json' => $data,
            ]);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            $this->handleException($e);
            return [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }

    private function handleResponse(ResponseInterface $response, array $params = []): ?array
    {
        $responseData = json_decode($response->getBody()->getContents(), true);
        $responseData['success'] = true;
        if (isset($responseData['meta'])) {
            $currentPage = data_get($params, 'page.number', 0);
            $responseData['meta']['current_page'] = $currentPage;
            $responseData['meta']['next_page'] = ($currentPage + 1) === $responseData['meta']['pageCount'] ? null : $currentPage + 1;
            $responseData['meta']['prev_page'] = $currentPage === 0 ? null : $currentPage - 1;
        }
        return $responseData;
    }

    private function handleException(GuzzleException $e): array
    {
        return [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'success' => false
        ];
    }
}
