<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Promocode
 *
 * @property int $id
 * @property string|null $promocode
 * @property int $client_id
 * @property int $discount
 * @property int $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Client $partner
 * @method static Builder|Promocode newModelQuery()
 * @method static Builder|Promocode newQuery()
 * @method static Builder|Promocode ofPartner($id)
 * @method static Builder|Promocode query()
 * @method static Builder|Promocode whereClientId($value)
 * @method static Builder|Promocode whereCreatedAt($value)
 * @method static Builder|Promocode whereDiscount($value)
 * @method static Builder|Promocode whereId($value)
 * @method static Builder|Promocode whereIsActive($value)
 * @method static Builder|Promocode wherePromocode($value)
 * @method static Builder|Promocode whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string|null $active_until
 * @property int $promocode_type_id
 * @property int $min_total
 * @property int|null $brand_id
 * @property array|null $required_products
 * @property int|null $free_product_id
 * @property-read array $promocode_type
 * @method static Builder|Promocode active()
 * @method static Builder|Promocode whereActiveUntil($value)
 * @method static Builder|Promocode whereBrandId($value)
 * @method static Builder|Promocode whereFreeProductId($value)
 * @method static Builder|Promocode whereMinTotal($value)
 * @method static Builder|Promocode wherePromocodeTypeId($value)
 * @method static Builder|Promocode whereRequiredProducts($value)
 */
class Promocode extends Model {
    const GOV_PROMOCODE_ID = 165;
    const TYPES = [
        1 => 'Процентный',
        2 => 'Фиксированный',
        3 => 'Подарок',
        4 => 'Каскадный процентный',
    ];
    const CONDITIONS = [
        1 => 'Без условия',
        2 => 'Минимальная сумма покупки',
        3 => 'Покупка бренда на сумму X',
        4 => 'Покупка категории на сумму X',
        5 => 'Покупка определенных товаров',
    ];
    const PURPOSES = [
        1 => 'Все товары',
        2 => 'Список товаров',
        3 => 'Категории',
        4 => 'Бренды',
    ];
    const CASCADES = [
        1 => 'От суммы',
        2 => 'От количества позиций'
    ];
    protected $guarded = ['id'];
    protected $casts = [//'required_products' => 'array',
        'promocode_cascade' => 'array',
        'promocode_purpose_payload' => 'array',
        'promocode_condition_payload' => 'array',
        'promocode_gifts' => 'array',
    ];

    public function partner() {
        return $this->belongsTo('App\Client', 'client_id')
            ->withTrashed()
            ->withDefault([
                'client_name' => 'Без партнера',
                'id' => null
            ]);
    }

    public function scopeOfPartner($query, $id) {
        $query->where('client_id', $id);
    }

    public function scopeActive($query) {
        $query->where(function ($query) {
                $query->whereDate('active_until', '>=', today())->orWhere('active_until', null);
            })->where('is_active', true);
    }

    public function getPromocodeTypeAttribute(): array {
        return ['id' => $this->promocode_type_id, 'name' => self::TYPES[$this->promocode_type_id],];
    }
}
