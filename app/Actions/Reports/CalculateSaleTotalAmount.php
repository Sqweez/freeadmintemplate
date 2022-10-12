<?php

namespace App\Actions\Reports;

use App\Sale;

class CalculateSaleTotalAmount {

    public function handle($sales) {
        return (collect($sales)->reduce(function ($a, $c){
            $price = intval(collect($c['products'])->reduce(function ($_a, $_c) use ($c) {
                $price = $_c['product_price'] - ($_c['product_price'] * ($_c['discount'] / 100));
                return $_a + $price;
            }, 0));
            if ($c['kaspi_red']) {
                $price -= $price * Sale::KASPI_RED_PERCENT;
            }
            return ceil($a + $price - $c['balance']);
        }, 0));
    }
}
