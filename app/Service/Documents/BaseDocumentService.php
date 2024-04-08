<?php

namespace App\Service\Documents;

use App\Document;
use App\Resolvers\LegalEntityResolver;
use App\Service\Documents\Products\ProductsResolverFactory;
use App\Service\Documents\Products\ProductsResolverInterface;
use App\v2\Models\WholesaleOrder;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

abstract class BaseDocumentService
{
    protected string $templatePath;
    protected LegalEntityResolver $legalEntityResolver;
    protected ProductsResolverInterface $productsResolver;
    protected int $documentType;
    protected Model $entity;

    const MONTHS_RU = [
        'Января', 'Февраля',
        'Марта', 'Апреля',
        'Мая', 'Июня',
        'Июля', 'Августа',
        'Сентября', 'Октября',
        'Ноября', 'Декабря',
    ];

    /**
     * @throws Exception
     */
    public function __construct(Model $entity)
    {
        $this->templatePath = $this->getTemplatePath();
        $this->legalEntityResolver = new LegalEntityResolver(__hardcoded(1));
        $this->legalEntityResolver->setBankAccount(__hardcoded(1));
        $this->entity = $entity;
        $this->productsResolver = ProductsResolverFactory::create($entity);
    }

    abstract public function getDocumentType(): int;

    abstract public function getTemplatePath();

    public function getDocumentDate(): string
    {
        return today()->format('d.m.Y');
    }

    public function loadTemplateFile(): Spreadsheet
    {
        return IOFactory::load($this->templatePath);
    }

    abstract public function create();

    protected function getDocumentNumber() {
        $document = Document::whereDocumentType($this->getDocumentType())->latest()->first();
        return $document ? $document->document_number + 1 : 1;
    }

    public function getCurrencyCode()
    {
        if ($this->entity instanceof WholesaleOrder) {
            return $this->entity->currency->code ?? __hardcoded('KZT');
        }
        return __hardcoded('KZT');
    }

    public function getFileName(): string
    {
        return Document::DOCUMENT_TYPES[$this->getDocumentType()] . "_" . Carbon::today()->toDateString() . "_" . Str::random(10) . '.xlsx';
    }
}
