<?php

namespace App\v2\Models;

use App\Store;
use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\ShiftTax
 *
 * @property int $id
 * @property int $shift_tax Стоимость смены
 * @property int $sale_percent Процент от продаж
 * @property int $store_id
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereSalePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereShiftTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftTax whereStoreId($value)
 * @mixin \Eloquent
 * @property-read mixed $calculated_sale_percent
 */
class ShiftTax extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'shift_tax' => 'integer',
        'sale_percent' => 'integer',
        'store_id' => 'integer'
    ];

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function getCalculatedSalePercentAttribute($value) {
        return $this->sale_percent / 100;
    }
}
