<?php

namespace App\DTO\Filters;

use App\DTO\Reports\BaseDTO;

class ClientFilterDTO extends BaseDTO
{
    public ?string $search;
    public $wholesales;
    public $partner;
    public $loyalty_id;
    public $is_partner;
    public $gender;
    public $client_city;
    public $is_wholesale_buyer;
    public $is_kaspi;

    public function __construct($payload) {
        $this->search = $payload['search'] ?? null;
        $this->wholesales = $payload['wholesales'] ?? null;
        $this->loyalty_id = $payload['loyalty_id'] ?? null;
        $this->partner = $payload['partner'] ?? null;
        $this->is_partner = (isset($payload['is_partner']) && $payload['is_partner'] === 'true') ?? null;
        $this->gender = $payload['gender'] ?? null;
        $this->client_city = $payload['client_city'] ?? null;
        $this->is_wholesale_buyer = (isset($payload['is_wholesale_buyer']) && $payload['is_wholesale_buyer'] === 'true') ?? null;
        $this->is_kaspi = (isset($payload['is_kaspi']) && $payload['is_kaspi'] === 'true') ?? null;
    }
}
