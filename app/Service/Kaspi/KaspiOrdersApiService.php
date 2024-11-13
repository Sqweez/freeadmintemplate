<?php

namespace App\Service\Kaspi;

class KaspiOrdersApiService extends BaseKaspiApiService
{
    public function getOrders(int $pageNumber = 0, array $filters = [], string $searchKey = null): ?array
    {
        $paginationQuery = [
            'number' => $pageNumber,
            'size' => __hardcoded(10)
        ];

        $defaultFilters = [
            'creationDate' => [
                '$ge' => now()->startOfDay()->timestamp * 1000,
                '$le' => now()->timestamp * 1000,
            ],
            'state' => 'DELIVERY',
        ];

        $queryFilters = array_merge($defaultFilters, $filters);
        if ($searchKey) {
            $queryFilters = array_merge(json_decode(base64_decode($searchKey), true), $queryFilters);
        }

        return $this->get('orders', [
            'page' => $paginationQuery,
            'filter' => [
                'orders' => $queryFilters
            ]
        ]);
    }

    public function getOrderById($orderId): ?array
    {
        return $this->get('orders', [
            'filters' => [
                'orders' => [
                    'code' => $orderId
                ]
            ]
        ]);
    }

    public function getOrderEntries($orderId): ?array
    {
        return $this->get("orders/{$orderId}/entries");
    }
}
