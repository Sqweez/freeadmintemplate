<?php

namespace App\Builder;

use App\Actions\Kaspi\CreateKaspiPriceAction;
use App\Repository\ProductRepository;
use App\Service\Ecommerce\ForteProductXMLGenerator;
use App\Service\Ecommerce\HalykProductExcelGenerator;
use App\Service\Ecommerce\HalykProductXMLGenerator;
use App\Service\Ecommerce\KaspiProductXMLGenerator;
use App\Service\Ecommerce\ProductXMLGenerator;
use App\v2\Models\KaspiEntity;
use Exception;

class CreateKaspiActionBuilder
{

    public const FORTE = 'FORTE';
    public const KASPI = 'KASPI';
    public const HALYK_XML = 'HALYK_XML';
    public const HALYK_EXCEL = 'HALYK_EXCEL';

    /**
     * @throws Exception
     */
    public static function build(KaspiEntity $kaspiEntity, $generatorType): CreateKaspiPriceAction
    {
        $productXMLGenerator = self::getXMLGenerator($generatorType);
        $productRepository = new ProductRepository();
        $extension = '.xml';
        if ($generatorType === self::HALYK_EXCEL) {
            $extension = '.xlsx';
        }
        return new CreateKaspiPriceAction($kaspiEntity, $productRepository, $productXMLGenerator, $extension);
    }

    /**
     * @throws Exception
     */
    private static function getXMLGenerator(string $type = 'KASPI'): ProductXMLGenerator
    {
        switch ($type) {
            case self::KASPI:
                return new KaspiProductXMLGenerator();
            case self::FORTE:
                return new ForteProductXMLGenerator();
            case self::HALYK_XML:
                return new HalykProductXMLGenerator();
            case self::HALYK_EXCEL:
                return new HalykProductExcelGenerator();
            default:
                throw new Exception('Неизвестный тип площадки');
        }
    }
}
