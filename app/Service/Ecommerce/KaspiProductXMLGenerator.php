<?php

namespace App\Service\Ecommerce;

use App\v2\Models\KaspiEntity;

class KaspiProductXMLGenerator implements ProductXMLGenerator
{
    public function generate(array $products, KaspiEntity $kaspiEntity): string
    {
        try {
            $xmlWriter = new \XMLWriter();
            $xmlWriter->openMemory();
            $xmlWriter->startDocument('1.0', 'UTF-8');
            $xmlWriter->startElement('kaspi_catalog');
            $xmlWriter->writeAttribute('date', date('Y-m-d'));
            $xmlWriter->writeAttribute('xmlns', 'kaspiShopping');
            $xmlWriter->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $xmlWriter->writeAttribute('xsi:schemaLocation', 'http://kaspi.kz/kaspishopping.xsd');
            $xmlWriter->writeElement('company', $kaspiEntity->company_name);
            $xmlWriter->writeElement('merchantid', $kaspiEntity->merchant_id);
            $xmlWriter->startElement('offers');
            foreach ($products as $product) {
                $this->addOffer($xmlWriter, $product);
            }

            $xmlWriter->endElement();
            $xmlWriter->endElement();
            $xmlWriter->endDocument();
            return $xmlWriter->outputMemory();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            throw new \RuntimeException('Ошибка при генерации XML: ' . $e->getMessage(), 0, $e);
        }
    }

    private function addOffer(\XMLWriter $xmlWriter, $product)
    {
        if (!isset($product['sku'], $product['product_name'], $product['brand'], $product['availabilities'], $product['price'])) {
            throw new \InvalidArgumentException('Некоторые обязательные поля продукта отсутствуют');
        }

        $xmlWriter->startElement('offer');
        $xmlWriter->writeAttribute('sku', $product['sku']);
        $xmlWriter->writeElement('model', $product['product_name']);
        $xmlWriter->writeElement('brand', $product['brand']);

        $this->addAvailabilities($xmlWriter, $product['availabilities']);

        $xmlWriter->writeElement('price', $product['price']);
        $xmlWriter->endElement(); 
    }

    private function addAvailabilities(\XMLWriter $xmlWriter, $availabilities)
    {
        $xmlWriter->startElement('availabilities');
        foreach ($availabilities as $availability) {
            $xmlWriter->startElement('availability');
            $xmlWriter->writeAttribute('available', $availability['available']);
            $xmlWriter->writeAttribute('storeId', $availability['storeId']);
            $xmlWriter->endElement();
        }
        $xmlWriter->endElement();
    }

    public function getBaseName(): string
    {
        return 'kaspi\xml\kaspi_products_';
    }
}
