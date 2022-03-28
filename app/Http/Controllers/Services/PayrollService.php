<?php


namespace App\Http\Controllers\Services;


use App\Http\Resources\Shifts\ShiftPenaltyResource;
use App\MarginType;
use App\Sale;
use App\User;
use App\v2\Models\ProductSaleEarning;
use App\v2\Models\Shift;
use App\v2\Models\ShiftPenalty;
use App\v2\Models\ShiftTax;
use Carbon\Carbon;

class PayrollService {
    private $start;
    private $finish;
    private $sellers;
    private $sellersIds;
    private $sales;
    private $shifts;
    private $taxes;
    private $penalties;
    private $productMarginTypes;

    public function __construct($date) {
        $this->start = Carbon::parse($date)->startOfMonth();
        $this->finish = Carbon::parse($date)->endOfMonth();
    }

    public function getPayroll() {
        $this->getNeedleData();
        return $this->sellers->map(function ($seller) {
            return $this->createPayroll($seller);
        });
    }

    private function getNeedleData() {
        $this->sellers = $this->getSellers();
        $this->sellersIds = $this->sellers->pluck('id')->all();
        $this->productMarginTypes = MarginType::all();
        $this->taxes = $this->getShiftTaxes();
        $this->sales = $this->getSales();
        $this->shifts = $this->getShifts();
        $this->penalties = $this->getShiftPenalties();
    }

    private function createPayroll($seller) {
        $sellerId = $seller['id'];
        $storeId = $seller['store_id'];
        //$salePercent = $this->taxes[$storeId]['sale_percent'] ?? 0;
        $shiftTax = $this->taxes[$storeId]['shift_tax'] ?? 0;
        $shiftCount = count($this->shifts[$sellerId] ?? []);
        $currentPenalties = collect($this->penalties[$sellerId] ?? []);
        $saleAmount =  $this->sales[$sellerId]['total_amount'] ?? 0;
        $saleAmountSalary = ceil($this->sales[$sellerId]['total_salary'] ?? 0);//ceil($saleAmount * $salePercent / 100);
        $calculations = $this->sales[$sellerId]['calculations'] ?? [];
        $shiftPenaltiesAmount = $currentPenalties->reduce(function ($a, $c) {
            return $a + $c['amount'];
        }, 0);

        return [
            'id' => $seller['id'],
            'name' => $seller['name'],
            'store_id' => $seller['store_id'],
            'sale_amount' => $saleAmount,
            'sale_amount_salary' => $saleAmountSalary,
            'shift_count' => $shiftCount,
            'shift_salary' => $shiftTax * $shiftCount,
            'shift_penalties_amount' => $shiftPenaltiesAmount,
            'shift_penalties_list' => ShiftPenaltyResource::collection($currentPenalties),
            'total_salary' => $saleAmountSalary + $shiftPenaltiesAmount + $shiftTax * $shiftCount,
            'calculations' => $calculations,
        ];
    }

    private function getSellers() {
        return User::sellers()
            ->select(['id', 'name', 'store_id'])
            ->get();
    }

    private function getSales() {
        return Sale::whereDate('created_at', '>=', $this->start)
            ->whereDate('created_at', '<=', $this->finish)
            ->whereIn('user_id', $this->sellersIds)
            ->with('products')
            ->with('products.product:id,product_id,margin_type_id')
            ->get()
            ->groupBy('user_id')
            ->map(function ($item, $key) {
                $productsArray = $this->getGroupProductsArray($item);
                $calculations = $this->getAmountByType($productsArray);
                return [
                    'calculations' => $calculations,
                    'total_amount' => $calculations->reduce(function ($a, $c) {
                        return $a + $c['amount'];
                    }, 0),
                    'total_salary' => $calculations->reduce(function ($a, $c) {
                        return $a + $c['salary'];
                    }, 0),
                    'salary' => 0,
                    'user_id' => $key,
                ];
            });
    }

    private function getShifts() {
        return Shift::whereDate('created_at', '>=', $this->start)
            ->whereDate('created_at', '<=', $this->finish)
            ->whereIn('user_id', $this->sellersIds)
            ->get()
            ->groupBy('user_id');
    }

    private function getShiftTaxes() {
        return ShiftTax::all()
            ->groupBy('store_id')
            ->map(function ($item) {
                return collect($item)->first();
            });
    }

    private function getShiftPenalties() {
        return ShiftPenalty::whereDate('created_at', '>=', $this->start)
            ->whereDate('created_at', '<=', $this->finish)
            ->get()
            ->groupBy('user_id');
    }

    private function getGroupProductsArray($sales): array {
        $products = [];
        foreach ($sales as $sale) {
            foreach ($sale['products'] as $product) {
                $products[] = [
                    'product_id' => $product['product_id'],
                    'price' => $product['final_sale_price'],
                    'margin_type_id' => $product['product']['margin_type_id']
                ];
            }
        }
        return $products;
    }

    private function getAmountByType($products): \Illuminate\Support\Collection {
        return collect($products)
            ->groupBy('margin_type_id')
            ->map(function ($product, $key) {
                $totalAmount = ceil(collect($product)->reduce(function ($a, $c) {
                    return $a + $c['price'];
                }, 0));
                $currentMarginType = $this->productMarginTypes->where('id', $key)->first();
                $salaryPercent = (collect($currentMarginType['salary_rules'])
                        ->sortByDesc('threshold')
                        ->values()
                        ->filter(function ($rule) use ($totalAmount) {
                            return $rule['threshold'] <= $totalAmount;
                        })
                        ->first()['value']) ?? 0;
                if ($salaryPercent !== 0) {
                    $salaryPercent = str_replace(',', '.', $salaryPercent);
                }
                $salaryPercent = floatval($salaryPercent) / 100;
                $salary = ceil($totalAmount * $salaryPercent);
                return [
                    'margin_type' => $currentMarginType['title'],
                    'amount' => $totalAmount,
                    'salary' => $salary,
                    'percent' => $salaryPercent * 100,
                ];
            })
            ->values()
            ->sortBy('margin_type')
            ->values();
    }

    private function getOwnFinalPrice() {

    }
}
