<?php

namespace App\Service\Kaspi;

class KaspiOrdersApiService extends BaseKaspiApiService
{
    private const DEFAULT_ORDER_STATE = 'NEW';

    public function getOrders(int $pageNumber = 0, array $filters = [], string $searchKey = null, int $perPage = 10): ?array
    {
        $paginationQuery = $this->buildPaginationQuery($pageNumber, $perPage);
        $queryFilters = $this->buildFilters($filters, $searchKey);
        return $this->get('orders', [
            'page' => $paginationQuery,
            'filter' => [
                'orders' => $queryFilters
            ],
            'included' => [
                'orders' => 'user',
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
            ],
            'included' => ['orders' => 'entries']
        ]);
    }

    public function getOrderEntries($orderId): ?array
    {
        return $this->get("orders/{$orderId}/entries");
    }

    public function getPointOfService($pointOfServiceId): ?array
    {
        return $this->get("pointofservices/$pointOfServiceId");
    }

    private function buildPaginationQuery(int $pageNumber, $perPage = null): array
    {
        return [
            'number' => $pageNumber,
            'size' => $perPage ?: self::DEFAULT_PAGE_SIZE,
        ];
    }

    private function buildFilters(array $filters, ?string $searchKey): array
    {
        $defaultFilters = [
            'creationDate' => [
                '$ge' => now()->subWeek()->startOfDay()->timestamp * 1000,
                '$le' => now()->timestamp * 1000,
            ],
            'state' => self::DEFAULT_ORDER_STATE,
        ];

        $queryFilters = array_merge($defaultFilters, $filters);

        if (isset($queryFilters['code']) && $queryFilters['code']) {
            $queryFilters = [
                'code' => $queryFilters['code']
            ];
        }

        if ($searchKey) {
            $decodedFilters = json_decode(base64_decode($searchKey), true);
            if (is_array($decodedFilters)) {
                $queryFilters = array_merge($queryFilters, $decodedFilters);
            }
        }

        return $queryFilters;
    }
}
