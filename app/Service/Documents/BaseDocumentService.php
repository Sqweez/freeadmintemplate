<?php

namespace App\Service\Documents;

abstract class BaseDocumentService
{
    protected string $templatePath;
    public function __construct()
    {
        $this->templatePath = $this->getTemplatePath();
    }

    abstract public function getTemplatePath();
}
