<?php

namespace App\Builder;

use App\Actions\Kaspi\CreateKaspiPriceAction;
use App\Repository\ProductRepository;
use App\Service\Ecommerce\ForteProductXMLGenerator;
use App\Service\Ecommerce\KaspiProductXMLGenerator;
use App\Service\Ecommerce\ProductXMLGenerator;
use App\v2\Models\KaspiEntity;
use Exception;

class CreateKaspiActionBuilder
{
    /**
     * @throws Exception
     */
    public static function build(KaspiEntity $kaspiEntity, $generatorType): CreateKaspiPriceAction
    {
        $productXMLGenerator = self::getXMLGenerator($generatorType);
        $productRepository = new ProductRepository();
        return new CreateKaspiPriceAction($kaspiEntity, $productRepository, $productXMLGenerator);
    }

    /**
     * @throws Exception
     */
    private static function getXMLGenerator(string $type = 'KASPI'): ProductXMLGenerator
    {
        switch ($type) {
            case 'KASPI':
                return new KaspiProductXMLGenerator();
            case 'FORTE':
                return new ForteProductXMLGenerator();
            default:
                throw new Exception('Неизвестный тип площадки');
        }
    }
}
