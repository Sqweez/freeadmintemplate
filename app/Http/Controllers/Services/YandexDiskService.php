<?php


namespace App\Http\Controllers\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class YandexDiskService {

    private $baseURI = 'https://cloud-api.yandex.net/v1/disk/resources/upload?';

    /**
     * @throws GuzzleException
     */
    public function upload($path, $url): \Psr\Http\Message\StreamInterface {
        $httpClient = new Client();
        $query = http_build_query([
            'path' => $path,
            'url' => $url
        ]);
        return $httpClient->post($this->getUploadURL($query), [
            'headers' => $this->getHeaders(),
        ])->getBody();
    }

    private function getHeaders(): array {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'OAuth ' . env('YANDEX_DISK_ACCESS_TOKEN')
        ];
    }

    private function getUploadURL($query): string {
        return $this->baseURI . $query;
    }
}
