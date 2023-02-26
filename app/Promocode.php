<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Promocode
 *
 * @property int $id
 * @property string|null $promocode
 * @property int $client_id
 * @property int $discount
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $partner
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode ofPartner($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode wherePromocode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $active_until
 * @property int $promocode_type_id
 * @property int $min_total
 * @property int|null $brand_id
 * @property array|null $required_products
 * @property int|null $free_product_id
 * @property-read array $promocode_type
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode active()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereActiveUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereFreeProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereMinTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode wherePromocodeTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereRequiredProducts($value)
 */
class Promocode extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'required_products' => 'array'
    ];

    const GOV_PROMOCODE_ID = 165;

    const TYPES = [
        1 => 'Процентный',
        2 => 'Фиксированный',
        5 => 'Процентный каскад',
        3 => 'Покупка определенных позиций',
        4 => 'Покупка определенного бренда на сумму',
    ];

    public function partner() {
        return $this->belongsTo('App\Client', 'client_id')->withTrashed()->withDefault([
            'client_name' => 'Без партнера',
            'id' => null,
        ]);
    }

    public function scopeOfPartner($query, $id) {
        $query->where('client_id', $id);
    }

    public function scopeActive($query) {
        $query
            ->where(function ($query) {
                $query
                    ->whereDate('active_until', '>=', today())
                    ->orWhere('active_until', null);
            })
            ->where('is_active', true);
    }

    public function getPromocodeTypeAttribute(): array {
        return [
            'id' => $this->promocode_type_id,
            'name' => self::TYPES[$this->promocode_type_id],
        ];
    }
}
