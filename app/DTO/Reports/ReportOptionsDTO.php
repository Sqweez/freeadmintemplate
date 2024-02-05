<?php

namespace App\DTO\Reports;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportOptionsDTO extends BaseDTO
{
    public string $start;
    public string $finish;
    public ?User $user;
    public $store_id;
    public $manufacturer_id;
    public $promocode_id;
    public ?User $currentUser;

    public function __construct(array $data)
    {
        $this->start = (Carbon::parse($data['start'] ?? today()))->startOfDay()->toDateTimeString();
        $this->finish = (Carbon::parse($data['finish'] ?? today()))->endOfDay()->toDateTimeString();
        $this->user = $data['user_id'] ? User::find($data['user_id']) : null;
        $this->store_id = $data['store_id'] ?? null;
        $this->manufacturer_id = $data['manufacturer_id'] ?? null;
        $this->promocode_id = $data['promocode_id'] ? json_decode($data['promocode_id']) : null;

        if ($this->promocode_id !== null && json_last_error() !== JSON_ERROR_NONE) {
            $this->promocode_id = null;
        }
    }

    public function setUser(?User $user)
    {
        $this->currentUser = $user;
    }

    protected static function mapDataFromRequest(Request $request): array
    {
        return [
            'start' => $request->get('start'),
            'finish' => $request->get('finish'),
            'user_id' => $request->get('user_id', null),
            'is_supplier' => $request->has('is_supplier'),
            'store_id' => $request->get('store_id', null),
            'manufacturer_id' => $request->get('manufacturer_id', null),
            'promocode_id' => $request->has('promocode_id') ? json_decode($request->get('promocode_id')) : null
        ];
    }
}
