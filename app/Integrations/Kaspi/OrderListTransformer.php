<?php

namespace App\Integrations\Kaspi;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderListTransformer
{
    public function __construct()
    {
    }

    public function transform(array $response): Collection
    {
        unset($response['data']);
        return collect(
            $response
        );
        return collect($response['data'])
            ->map(function ($order) {
                return $this->transformItem($order);
            });
    }

    public function transformItem(array $order): array
    {
        $attributes = $order['attributes'];
        return [
            'kaspi_id' => $order['id'],
            'client' => $this->getClientInfo($order),
            'payment_type' => $this->getPaymentType($attributes['paymentMode']),
            'creation_date_formatted' => format_datetime(
                Carbon::createFromTimestamp($attributes['creationDate'] / 1000)
            ),
            'code' => $attributes['code'],
            'total_price' => price_format($attributes['totalPrice']),
            'signature_required' => $attributes['signatureRequired'] ? 'Да' : 'Нет',
            'entries_id' => collect(data_get($order, 'relationships.entries.data', []))->pluck('id'),
            'pickup_point_id' => data_get($attributes, 'pickupPointId'),
            'entries' => data_get($order, 'entries'),
            'external_url' => sprintf("https://kaspi.kz/mc/#/orders/%s", $attributes['code']),
            'status' => $this->getOrderStatus($attributes['status']),
            'state' => $attributes['state'],
            'products' => $this->getProducts($order),
            'store' => $attributes['pickupPointId']
        ];
    }

    private function getProducts($order): Collection
    {
        $entriesId = collect(data_get($order, 'relationships.entries.data', []))->pluck('id');
        $products = collect();
        foreach ($entriesId as $entryId) {
            #$product = self::$included->where('id', $entryId)->first();
            #$products->push($product);
        }
        return $products;
    }

    private function getOrderStatus($status)
    {
        $statuses = [
            "APPROVED_BY_BANK" => "Продавец должен его принять",
            "ACCEPTED_BY_MERCHANT" => "Принят",
            "COMPLETED" => "Завершен",
            "CANCELLED" => "Отменен",
            "CANCELLING" => "В процессе отмены",
            "KASPI_DELIVERY_RETURN_REQUESTED" => "Ожидает возврата",
            "RETURNED" => "Возвращен"
        ];

        return data_get($statuses, $status, 'Неизвестно');
    }

    private function getClientInfo(array $order)
    {
        $customer = data_get($order, 'attributes.customer');
        return $customer + [
                'fullName' => trim(
                    sprintf(
                        "%s %s",
                        data_get($customer, 'firstName'),
                        #data_get($customer, 'name'),
                        data_get($customer, 'lastName'),
                    )
                )
            ];
    }

    private function getPaymentType(string $paymentType)
    {
        $paymentTypes = [
            'PREPAID' => 'Безналичная оплата',
            'PAY_WITH_CREDIT' => 'Оплата в кредит'
        ];

        $current = $paymentTypes[$paymentType];
        return $current ?: 'Неизвестно';
    }

    private function getAddressInfo(array $attributes): array
    {
        $pickupPoint = self::$pickupPoints->get($attributes['pickupPointId'])['address'];
        return [
            'destination_point' => $this->getDestinationPoint($attributes, $pickupPoint),
            'pickup_point' => $pickupPoint,
        ];
    }

    private function getDestinationPoint(array $attributes, $pickupPoint)
    {
        if (isset($attributes['deliveryAddress'])) {
            return $attributes['deliveryAddress']['formattedAddress'];
        }
        if ($attributes['deliveryMode'] === 'DELIVERY_PICKUP') {
            if ($attributes['isKaspiDelivery'] === false) {
                return $pickupPoint;
            } else {
                return 'Kaspi Postomat';
            }
        }
        return __hardcoded('Неизвестно');
    }
}
