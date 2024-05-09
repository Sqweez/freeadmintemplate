<?php

namespace App\Service\Ecommerce;

use App\v2\Models\KaspiEntity;

interface ProductXMLGenerator
{
    public function generate(array $products, KaspiEntity $kaspiEntity): string;
    
    public function getBaseName(): string;
}
