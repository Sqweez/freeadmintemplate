<?php

namespace App\Service\Sales;

use App\Service\Sales\Utils\CashbackEvaluator;

class SaleService
{
    private CashbackEvaluator $cashbackEvaluator;
    private SaleCreationService $saleCreationService;
    private ClientSaleService $clientSaleService;
    private CompanionSaleService $companionSaleService;

    public function __construct(
        CashbackEvaluator $cashbackEvaluator,
        SaleCreationService $saleCreationService,
        ClientSaleService $clientSaleService,
        CompanionSaleService $companionSaleService
    ) {
        $this->cashbackEvaluator = $cashbackEvaluator;
        $this->saleCreationService = $saleCreationService;
        $this->clientSaleService = $clientSaleService;
        $this->companionSaleService = $companionSaleService;
    }

    public function process()
    {
        // @TODO 2024-08-26T16:04 implement

    }
}
