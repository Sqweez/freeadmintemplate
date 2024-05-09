<?php

namespace App\Service\Ecommerce;

use App\v2\Models\KaspiEntity;

class ForteProductXMLGenerator implements ProductXMLGenerator
{

    public function generate(array $products, KaspiEntity $kaspiEntity): string
    {
        try {
            $xmlWriter = new \XMLWriter();
            $xmlWriter->openMemory();
            $xmlWriter->startDocument('1.0', 'UTF-8');
            $xmlWriter->startElement('fm_catalog');
            $xmlWriter->writeAttribute('date', now()->format('Y-m-d H:i'));
            $xmlWriter->writeElement('company', 'IRON-ADDICTS KZ');
            $xmlWriter->startElement('shop');
            $xmlWriter->writeElement('merchant-id', config('ecommerce.forte_merchant_id'));
            $xmlWriter->startElement('offers');
            foreach ($products as $product) {
                $this->addOffer($xmlWriter, $product);
            }
            $xmlWriter->endElement();
            $xmlWriter->endElement();
            $xmlWriter->endElement();
            $xmlWriter->endDocument();
            return $xmlWriter->outputMemory();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            throw new \RuntimeException('Ошибка при генерации XML: ' . $e->getMessage(), 0, $e);
        }
    }

    private function addOffer(\XMLWriter $XMLWriter, $product)
    {
        if (!isset($product['sku'], $product['product_name'], $product['brand'], $product['availabilities'], $product['price'])) {
            throw new \InvalidArgumentException('Некоторые обязательные поля продукта отсутствуют');
        }
        $inStock = collect($product['availabilities'])->where('available', 'yes')->count() > 0;
        if (!$inStock) {
            return ;
        }
        $XMLWriter->startElement('offer');
        $XMLWriter->writeAttribute('sku', $product['sku']);
        $XMLWriter->writeElement('name', $this->escapeXML($product['product_name']));
        $XMLWriter->writeElement('vendor', $product['brand']);
        $XMLWriter->startElement('pickup-options');
        foreach ($product['availabilities'] as $availability) {
            $this->addPickupOption($XMLWriter, $availability);
        }
        $XMLWriter->endElement();
        $XMLWriter->writeElement('price', $product['price']);
        $XMLWriter->endElement();
    }

    public function getBaseName(): string
    {
        return 'forte\xml\forte_products_';
    }

    private function addPickupOption(\XMLWriter $XMLWriter, $availability)
    {
        if ($availability['available'] === __hardcoded('no')) {
            return ;
        }
        $XMLWriter->startElement('pickup-option');
        $XMLWriter->writeAttribute('id', $availability['storeId']);
        $XMLWriter->endElement();
    }

    private function escapeXML($string) {
        $string = str_replace("&", "&amp;", $string);
        $string = str_replace("<", "&lt;", $string);
        $string = str_replace(">", "&gt;", $string);
        $string = str_replace('"', "&quot;", $string);
        $string = str_replace("'", "&apos;", $string);
        return $string;
    }
}
