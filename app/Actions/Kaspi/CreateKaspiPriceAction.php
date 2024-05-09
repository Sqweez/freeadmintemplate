<?php

namespace App\Actions\Kaspi;

use App\Repository\ProductRepository;
use App\Service\Ecommerce\ProductXMLGenerator;
use App\v2\Models\KaspiEntity;
use Illuminate\Http\Response;

class CreateKaspiPriceAction
{

    public const FILE_EXT = '.xml';

    private KaspiEntity $kaspiEntity;
    private ProductRepository $productRepository;
    private ProductXMLGenerator $productXMLGenerator;

    public function __construct(
        KaspiEntity $kaspiEntity,
        ProductRepository $productRepository,
        ProductXMLGenerator $productXMLGenerator
    ) {
        $this->kaspiEntity = $kaspiEntity;
        $this->productRepository = $productRepository;
        $this->productXMLGenerator = $productXMLGenerator;
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
        return sprintf("%s%s%s", $this->productXMLGenerator->getBaseName(), $this->kaspiEntity->id, self::FILE_EXT);
    }

    private function storeFile($content, $path): Response
    {
        \Storage::disk('public')->put($path, $content);
        return (new Response('success', 200))->header('Last-Modified', now()->toRfc822String());
    }

    private function getProductsXML(): string
    {
        $products = $this->productRepository->getVisibleProductsForKaspiEntity($this->kaspiEntity->id);
        return $this->productXMLGenerator->generate($products, $this->kaspiEntity);
    }
}
