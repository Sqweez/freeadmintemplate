<?php

namespace App\Actions\Kaspi;

use App\Repository\ProductRepository;
use App\Service\Kaspi\KaspiProductXMLGenerator;
use App\v2\Models\KaspiEntity;
use Illuminate\Http\Response;

class CreateKaspiPriceAction
{

    const BASE_NAME = 'kaspi\xml\kaspi_products_';
    const FILE_EXT = '.xml';

    private KaspiEntity $kaspiEntity;
    private ProductRepository $productRepository;
    private KaspiProductXMLGenerator $kaspiProductXMLGenerator;

    public function __construct(
        KaspiEntity $kaspiEntity,
        ProductRepository $productRepository,
        KaspiProductXMLGenerator $kaspiProductXMLGenerator
    ) {
        $this->kaspiEntity = $kaspiEntity;
        $this->productRepository = $productRepository;
        $this->kaspiProductXMLGenerator = $kaspiProductXMLGenerator;
    }

    public function handle(): Response
    {
        return $this->getKaspiProductXML();
    }

    public function getKaspiProductXML(): Response
    {
        $xmlContent = $this->getProductsXML();
        return $this->storeFile($xmlContent, $this->getFileName());
    }

    private function getFileName(): string
    {
        return sprintf("%s%s%s", self::BASE_NAME, $this->kaspiEntity->id, self::FILE_EXT);
    }

    private function storeFile($content, $path): Response
    {
        \Storage::disk('public')->put($path, $content);
        return (new Response('success', 200))->header('Last-Modified', now()->toRfc822String());
    }

    private function getProductsXML(): string
    {
        $products = $this->productRepository->getVisibleProductsForKaspiEntity($this->kaspiEntity->id);
        return $this->kaspiProductXMLGenerator->generate($products, $this->kaspiEntity);
    }
}
