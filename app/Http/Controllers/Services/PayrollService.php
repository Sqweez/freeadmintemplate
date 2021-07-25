<?php


namespace App\Http\Controllers\Services;


use App\Http\Resources\Shifts\ShiftPenaltyResource;
use App\Sale;
use App\User;
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
        $this->sales = $this->getSales();
        $this->shifts = $this->getShifts();
        $this->taxes = $this->getShiftTaxes();
        $this->penalties = $this->getShiftPenalties();
    }

    private function createPayroll($seller) {
        $sellerId = $seller['id'];
        $storeId = $seller['store_id'];
        $salePercent = $this->taxes[$storeId]['sale_percent'] ?? 0;
        $shiftTax = $this->taxes[$storeId]['shift_tax'] ?? 0;
        $shiftCount = count($this->shifts[$sellerId] ?? []);
        $currentPenalties = collect($this->penalties[$sellerId] ?? []);
        $saleAmount =  $this->sales[$sellerId]['amount'] ?? 0;
        $saleAmountSalary = ceil($saleAmount * $salePercent / 100);
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
            'total_salary' => $saleAmountSalary + $shiftPenaltiesAmount + $shiftTax * $shiftCount
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
            ->get()
            ->groupBy('user_id')
            ->map(function ($item, $key) {
                return [
                    'amount' => collect($item)->reduce(function ($a, $c) {
                        return $a + collect($c['products'])->reduce(function ($_a, $_c) {
                            return $_a + $_c['product_price'];
                        }, 0);
                    }, 0),
                    'user_id' => $key
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
}
