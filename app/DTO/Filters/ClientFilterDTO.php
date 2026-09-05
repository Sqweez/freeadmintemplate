<?php

namespace App\DTO\Filters;

use App\DTO\Reports\BaseDTO;

class ClientFilterDTO extends BaseDTO
{
    public ?string $search;
    public $wholesales;
    public $partner;
    public $loyalty_id;
    public ?bool $is_partner;
    public $gender;
    public $client_city;
    public ?bool $is_wholesale_buyer;
    public ?bool $is_kaspi;
    public ?bool $without_code;

    public function __construct($payload) {
        $this->search = $payload['search'] ?? null;
        $this->wholesales = $payload['wholesales'] ?? null;
        $this->loyalty_id = $payload['loyalty_id'] ?? null;
        $this->partner = $payload['partner'] ?? null;
        $this->is_partner = $this->nullableBoolean($payload, 'is_partner');
        $this->gender = $payload['gender'] ?? null;
        $this->client_city = $payload['client_city'] ?? null;
        $this->is_wholesale_buyer = $this->nullableBoolean($payload, 'is_wholesale_buyer');
        $this->is_kaspi = $this->nullableBoolean($payload, 'is_kaspi');
        $this->without_code = $this->nullableBoolean($payload, 'without_code');
    }

    private function nullableBoolean(array $payload, string $key): ?bool
    {
        if (!array_key_exists($key, $payload)) {
            return null;
        }

        return filter_var($payload[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
