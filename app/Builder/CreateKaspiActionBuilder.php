<?php

namespace App\Builder;

use App\Actions\Kaspi\CreateKaspiPriceAction;
use App\Repository\ProductRepository;
use App\Service\Kaspi\KaspiProductXMLGenerator;
use App\v2\Models\KaspiEntity;

class CreateKaspiActionBuilder
{
    public static function build(KaspiEntity $kaspiEntity): CreateKaspiPriceAction
    {
        $kaspiProductXMLGenerator = new KaspiProductXMLGenerator();
        $productRepository = new ProductRepository();
        return new CreateKaspiPriceAction($kaspiEntity, $productRepository, $kaspiProductXMLGenerator);
    }
}
