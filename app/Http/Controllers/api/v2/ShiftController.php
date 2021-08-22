<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\PayrollService;
use App\Http\Resources\Shifts\ShiftPenaltyResource;
use App\Store;
use App\User;
use App\v2\Models\Shift;
use App\v2\Models\ShiftPenalty;
use App\v2\Models\ShiftTax;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function getPayroll(Request $request) {
        $date = $request->get('date', now());
        return (new PayrollService($date))->getPayroll();
    }

    public function editShift(Shift $shift, Request $request) {
        $editMode = intval($request->get('editMode'));
        $user_id = $request->get('user_id');

        switch ($editMode) {
            case Shift::SHIFT_EDIT_REPLACE:
                $shift->update(['user_id' => $user_id]);
                break;
            case Shift::SHIFT_EDIT_CREATE:
                Shift::create([
                    'store_id' => $shift->store_id,
                    'user_id' => $user_id,
                    'created_at' => $shift->created_at,
                    'updated_at' => $shift->updated_at
                ]);
                break;
            case Shift::SHIFT_EDIT_DELETE:
                $shift->delete();
                break;
            default:
                break;
        }
    }

    public function getShifts(Request $request) {
        $date = $request->get('date', now());
        $daysInMonth = Carbon::parse($date)->daysInMonth;
        $dateArray = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = ($i > 9) ? $i : "0" . $i;
            $dateArray[] = $date . "-" . $day;
        }
        $shifts = Shift::whereDate('created_at', '>=', Carbon::parse($date)->startOfMonth())
            ->whereDate('created_at', '<=', Carbon::parse($date)->endOfMonth())
            ->with('user:id,name,store_id')
            ->get();
        $shops = Store::shops()->select(['id', 'name', 'city'])->get();
        return $shops->map(function ($shop) use ($shifts, $dateArray) {
            return collect($dateArray)->map(function ($date) use ($shifts, $shop) {
                $needleShift = $shifts->filter(function ($i) use ($date, $shop) {
                    return Carbon::parse($i['created_at'])->toDateString() === $date && $i['store_id'] === $shop['id'] && !is_null($i['user']);
                })->values();
                return [
                    'date' => $date,
                    'shop_id' => $shop['id'],
                    'shifts' => $needleShift,
                ];
            });
        });
    }

    public function createShift(Request $request) {
        $user_id = $request->get('user_id');
        $store_id = $request->get('store_id');
        $shift = Shift::whereUserId($user_id)
            ->whereStoreId($store_id)
            ->whereDate('created_at', now())
            ->get()
            ->first();

        return $shift ? $shift : Shift::create([
            'user_id' => $user_id,
            'store_id' => $store_id
        ]);
    }

    public function createShiftAdmin(Request $request) {
        $user_id = $request->get('user_id');
        $store_id = $request->get('store_id');
        $date = Carbon::parse($request->get('date'));
        return Shift::create([
            'user_id' => $user_id,
            'store_id' => $store_id,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    public function getShiftTaxes() {
        $shiftTaxes = ShiftTax::with('store:id,name,city')->get();
        $stores = Store::shops()->select(['id', 'name', 'city'])->get();
        $storesWithoutTaxes = collect($stores)->filter(function ($i) use ($shiftTaxes) {
            return !collect($shiftTaxes)->contains(function ($value) use ($i) {
                return $value['store_id'] === $i['id'];
            });
        })->values();
        return $shiftTaxes->mergeRecursive(
            collect($storesWithoutTaxes)->map(function ($i) {
                return [
                    'store_id' => $i['id'],
                    'store' => $i,
                    'sale_percent' => 0,
                    'shift_tax' => 0
                ];
            })
        );
    }

    public function updateShiftTaxes(Request $request) {
        ShiftTax::truncate();
        $shiftTaxes = $request->get('taxes');
        foreach ($shiftTaxes as $shiftTax) {
            ShiftTax::create($shiftTax);
        }
        return $this->getShiftTaxes();
    }

    public function getPenalties() {
        return ShiftPenaltyResource::collection(
            ShiftPenalty::with(['user', 'author'])->get()
        );
    }

    public function createPenalty(Request $request) {
        $penalty = ShiftPenalty::create($request->all());
        return new ShiftPenaltyResource($penalty);
    }

    public function deletePenalty($id) {
        try {
            ShiftPenalty::find($id)->delete();
        } catch (\Exception $e) {
        }
    }
}
