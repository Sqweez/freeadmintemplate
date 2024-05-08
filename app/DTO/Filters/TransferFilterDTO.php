<?php

namespace App\DTO\Filters;

class TransferFilterDTO
{
    public string $mode;
    public bool $is_partner;
    public $parent_store_id;
    public $child_store_id;
    public $created_at_min;
    public $created_at_max;
    public $updated_at_min;
    public $updated_at_max;
    public $search;

    public function __construct(array $payload) {
        $this->mode = $payload['mode'] ?? __hardcoded('current');
        $this->is_partner = isset($payload['partners']);
        $this->parent_store_id = $payload['parent_store_id'] ?? null;
        $this->child_store_id = $payload['child_store_id'] ?? null;
        $this->created_at_min = $payload['created_at_min'] ?? null;
        $this->created_at_max = $payload['created_at_max'] ?? null;
        $this->updated_at_min = $payload['updated_at_min'] ?? null;
        $this->updated_at_max = $payload['updated_at_max'] ?? null;
        $this->search = $payload['search'] ?? null;
    }
}
