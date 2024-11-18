<?php

namespace App\Http\Resources\Kaspi;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class KaspiOrderResource extends JsonResource
{
    protected static Collection $pickupPoints;
    protected static Collection $included;
    protected static $stores;

    public static function setAdditionalData(Collection $pickupPoints, Collection $included, $stores): void
    {
        self::$pickupPoints = $pickupPoints;
        self::$included = $included;
        self::$stores = $stores;
    }

    public function toArray($request): array
    {
        $order = $this->resource;
        $attributes = $order['attributes'];
        return [
                'kaspi_id' => $order['id'],
                'client' => $this->getClientInfo($order),
                'payment_type' => $this->getPaymentType($attributes['paymentMode']),
                'creation_date_formatted' => format_datetime(
                    Carbon::createFromTimestamp($attributes['creationDate'] / 1000)
                ),
                'delivery_type' => $this->getDeliveryType($attributes['deliveryMode'], $attributes['isKaspiDelivery']),
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
                'store' => self::$stores->where('id', self::$pickupPoints->get($attributes['pickupPointId'])['store_id'])->first(),
            ] + $this->getAddressInfo($attributes);
    }

    private function getProducts($order): Collection
    {
        $entriesId = collect(data_get($order, 'relationships.entries.data', []))->pluck('id');
        $products = collect();
        foreach ($entriesId as $entryId) {
            $product = self::$included->where('id', $entryId)->first();
            $products->push($product);
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

    private function getDeliveryType($deliveryMode, bool $isKaspiDelivery)
    {
        if ($isKaspiDelivery) {
            $types = [
                'DELIVERY_LOCAL' => 'Kaspi Доставка (по городу)',
                'DELIVERY_PICKUP' => 'Kaspi Доставка в Postomat',
                'DELIVERY_REGIONAL_TODOOR' => 'Kaspi Доставка',
            ];
        } else {
            $types = [
                'DELIVERY_LOCAL' => 'Курьером',
                'DELIVERY_PICKUP' => 'Самовывоз',
                'DELIVERY_REGIONAL_PICKUP' => 'Доставка по области до склада',
            ];
        }

        return $types[$deliveryMode];
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
