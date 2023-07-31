<?php

namespace App\Http\Controllers\api\v3;

use App\Http\Controllers\api\BaseApiController;
use App\Store;
use App\User;
use App\v2\Models\WorkingSchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkingScheduleController extends BaseApiController
{
    public function getMySchedule(Request $request)
    {
        $date = $request->get('date');
        abort_if(!$date, 403, 'Выберите дату!');
        $user = auth()->user();

        return $this->respondSuccess(
            [
                'period' => $this->getPeriodByMonth($date),
                'schedule' => $this->getScheduleByStoreId($user->store_id, $date),
            ]
        );
    }

    public function getScheduleList(Request $request): JsonResponse
    {
        $date = $request->has('date') ? Carbon::parse($request->get('date')) : today();
        abort_if(!$date, 403, 'Please provide a date');
        $stores = Store::query()->shops()->select(['id', 'name'])->get();
        $schedule = WorkingSchedule::query()
            ->whereDate('date', $date)
            ->with(['user'])
            ->get();

        return $this->respondSuccess([
            'list' => $stores->map(function (Store $store) use ($schedule) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'schedule' => $schedule->where('store_id', $store->id),
                ];
            }),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function destroy(WorkingSchedule $schedule): JsonResponse
    {
        $schedule->delete();
        return $this->respondSuccess();
    }

    public function store(Request $request): JsonResponse
    {
        /* @var User $user */
        $user = auth()->user();
        $date = $request->get('date');
        abort_if(!$date, 403, 'Укажите дату');
        WorkingSchedule::updateOrCreate([
            'user_id' => $user->id,
            'date' => Carbon::parse($date)->toDate(),
            'store_id' => $user->store_id,
        ], []);
        return $this->getMySchedule($request);
    }

    public function getPeriodByMonth(string $date): array
    {
        $from = Carbon::parse($date)->startOfMonth();
        $to = Carbon::parse($date)->endOfMonth();

        $carbonPeriod = CarbonPeriod::create($from, $to);
        $range = [];
        foreach ($carbonPeriod as $item) {
            $range[] = $item->format('d.m.Y');
        }

        return $range;
    }

    public function getScheduleByStoreId(int $storeId, string $date)
    {
        $date = Carbon::parse($date);
        return WorkingSchedule::query()->whereStoreId($storeId)->whereMonth('date', $date->month)->whereYear(
                'date',
                $date->year
            )->with(['user'])->get()->groupBy(function (WorkingSchedule $schedule) {
                return Carbon::parse($schedule->date)->format('d.m.Y');
            });
    }
}
