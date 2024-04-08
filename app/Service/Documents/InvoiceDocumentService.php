<?php

namespace App\Service\Documents;

use App\Document;

class InvoiceDocumentService extends BaseDocumentService
{
    public function getTemplatePath(): string
    {
        return storage_path('app/document_templates/invoice_payment_template.xlsx');
    }

    public function getDocumentType(): int
    {
        return Document::DOCUMENT_INVOICE_PAYMENT;
    }

    public function create()
    {
        $template = $this->loadTemplateFile();
        $activeSheet = $template->getActiveSheet();
    }
}
