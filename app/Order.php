<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Order
 *
 * @property int $id
 * @property string $user_token
 * @property int $payment
 * @property int $delivery
 * @property string $fullname
 * @property string $address
 * @property string $phone
 * @property string $city
 * @property string|null $email
 * @property string $store_id
 * @property string|null $comment
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $client_id
 * @property int $discount
 * @property int|null $balance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OrderProduct[] $items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Store $store
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\City $city_text
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserToken($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_paid
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsPaid($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @property-read Model|\Eloquent $image
 * @property-read int|null $image_count
 * @property int $is_iherb
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsIherb($value)
 * @property int|null $promocode_id
 * @property int $promocode_fixed_discount
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromocodeFixedDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromocodeId($value)
 * @property int $promocode_fixed_amount
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromocodeFixedAmount($value)
 */
class Order extends Model
{
    protected $guarded = [];

    use SoftDeletes;

    protected $casts = [
        'status' => 'integer',
        'id' => 'integer',
        'store_id' => 'integer',
        'client_id' => 'integer',
        'payment' => 'integer',
        'delivery' => 'integer',
        'city' => 'integer',
        'is_paid' => 'boolean'
    ];

    public const ORDER_STATUS = [
        0 => [
            'text' => 'В обработке'
        ],
        1 => [
            'text' => 'Выполнен'
        ],
        -1 => [
            'text' => 'Отменен'
        ],
    ];

    public const ORDER_DELIVERY = [
        0 => [
            'text' => 'Доставка курьером'
        ],
        1 => [
            'text' => 'Самовывоз'
        ]
    ];

    public const ORDER_PAYMENT = [
        0 => [
            'text' => 'Оплата наличными'
        ],
        1 => [
            'text' => 'Оплата картой'
        ],
        2 => [
            'text' => 'Онлайн-оплата'
        ]
    ];

    public const ORDER_PAYMENT_CASH = 0;
    public const ORDER_PAYMENT_CARD = 1;
    public const ORDER_PAYMENT_ONLINE = 2;

    public function items()
    {
        return $this->hasMany('App\OrderProduct', 'order_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id')->withDefault([
            'name' => 'Iron Addicts | Казахстан'
        ]);
    }

    public function city_text()
    {
        return $this->belongsTo('App\v2\Models\City', 'city');
    }

    public function image()
    {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

}
