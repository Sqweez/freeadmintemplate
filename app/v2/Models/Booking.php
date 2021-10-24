<?php

namespace App\v2\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\v2\Models\Booking
 *
 * @property int $id
 * @property int $user_id
 * @property int $client_id
 * @property int $store_id
 * @property int $arrival_id
 * @property bool $is_sold
 * @property int $paid_sum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Arrival $arrival
 * @property-read \App\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\BookingProduct[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereArrivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereIsSold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePaidSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUserId($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $date_create
 * @property-read mixed $status
 * @method static \Illuminate\Database\Query\Builder|Booking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Booking withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Booking withoutTrashed()
 */
class Booking extends Model
{

    use SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'store_id' => 'integer',
        'user_id' => 'integer',
        'arrival_id' => 'integer',
        'client_id' => 'integer',
        'paid_sum' => 'integer',
    ];

    const STATUSES = [
        0 => 'Ожидание',
        1 => 'Продан',
        -1 => 'Отменен'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }

    public function products() {
        return $this->hasMany('App\v2\Models\BookingProduct');
    }

    public function arrival() {
        return $this->belongsTo('App\Arrival');
    }

    public function getDateCreateAttribute() {
        return Carbon::parse($this->attributes['created_at'])->format('d.m.Y H:i:s');
    }

    public function getStatusAttribute() {
        return self::STATUSES[+$this->attributes['is_sold']];
    }
}
