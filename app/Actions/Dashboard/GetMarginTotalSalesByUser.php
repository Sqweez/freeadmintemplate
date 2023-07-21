<?php

namespace App\Actions\Dashboard;

use App\Concerns\UseQuickBindings;
use App\MarginType;
use App\Sale;

class GetMarginTotalSalesByUser
{
    use UseQuickBindings;

    public function handle($userId, $from, $to)
    {
        $sales = Sale::query()->whereUserId($userId)->whereDate('created_at', '>=', $from)->whereDate(
                'created_at',
                '<=',
                $to
            )->with(['products.product:id,margin_type_id'])->get();

        return $this->transformData($sales);
    }

    private function transformData($sales)
    {
        $marginTypes = MarginType::all();


        $saleProducts = $sales->map(function ($sale) {
                return $sale->products;
            })->flatten(1);

        $total = collect($saleProducts)->reduce(function ($a, $c) {
                return $a + $c->final_price;
            }, 0);

        $groupedSales = $saleProducts->groupBy('product.margin_type_id');

        return $marginTypes->map(function ($type) use ($groupedSales, $total) {

            $currentTotal = isset($groupedSales[$type->id]) ? collect($groupedSales[$type->id])->reduce(
                function ($a, $c) {
                    return $a + $c->final_price;
                },
                0
            ) : 0;

            return [
                'id' => $type->id,
                'title' => sprintf('Категория %s', $type->title),
                'total' => $currentTotal,
                'percent' => 100 * $currentTotal / $total,
            ];
        });
    }
}
