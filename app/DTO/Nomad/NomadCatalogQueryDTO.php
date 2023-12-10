<?php

namespace App\DTO\Nomad;

class NomadCatalogQueryDTO
{
    public $category_id;
    public $subcategory_id;
    public $store_id;
    public $brand_id;

    public function __construct(array $params)
    {
        $this->category_id = $params['category_id'] ?? null;
        $this->subcategory_id = $params['subcategory_id'] ?? null;
        $this->store_id = $params['store_id'] ?? null;
        $this->brand_id = $params['brand_id'] ?? null;
    }
}
