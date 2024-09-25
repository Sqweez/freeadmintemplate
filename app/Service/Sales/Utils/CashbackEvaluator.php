<?php

namespace App\Service\Sales\Utils;

class CashbackEvaluator
{
    public function isCashbackApplicable(): bool
    {
        return true;
    }

    public function calculateCashback(): int
    {
        return 0;
    }
}
