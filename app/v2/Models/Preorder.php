<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Preorder
 *
 * @property int $id
 * @property int $client_id
 * @property int $user_id
 * @property int $store_id
 * @property int $payment_type
 * @property int $status 0 - в ожидании, 1 - подтверждено, -1 - отменено
 * @property string|null $comment
 * @property int $amount
 * @property int $sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\PreorderProduct[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder query()
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Preorder whereUserId($value)
 * @mixin \Eloquent
 */
class Preorder extends Model
{
    const PREORDER_STATUS = [
        -1 => [
            'text' => 'Отменен'
        ],
        0 => [
            'text' => 'Новый'
        ],
        1 => [
            'text' => 'Выполнен'
        ]
    ];

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'amount' => 'integer',
        'payment_type' => 'integer',
        'status' => 'integer',
        'user_id' => 'integer',
        'store_id' => 'integer',
        'client_id' => 'integer',
        'sale_id' => 'integer'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id')->withDefault([
            'name' => 'Неизвестно',
            'id' => -1
        ])->withTrashed();
    }

    public function products() {
        return $this->hasMany('App\v2\Models\PreorderProduct', 'preorder_id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id')
            ->withDefault([
                'name' => 'Iron Addicts - Казахстан',
                'id' => -1,
            ])
            ->withTrashed();
    }

    public function client() {
        return $this->belongsTo('App\Client', 'client_id')->withDefault([
            'client_name' => 'Гость',
            'id' => -1
        ])->withTrashed();
    }

}
