<?php


namespace App\Http\Controllers\Services;

class YandexDiskService {

    private $baseURI = 'https://cloud-api.yandex.net/v1/disk/resources/upload?';

    public function upload($path, $url) {
        $httpClient = new \GuzzleHttp\Client();
        $query = http_build_query([
            'path' => $path,
            'url' => $url
        ]);
        return $httpClient->post($this->getUploadURL($query), [
            'headers' => $this->getHeaders(),
        ])->getBody();
    }

    private function getHeaders() {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'OAuth ' . env('YANDEX_DISK_ACCESS_TOKEN')
        ];
    }

    private function getUploadURL($query) {
        return $this->baseURI . $query;
    }
}
