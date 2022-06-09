<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Arrival
 *
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 * @property int $is_completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $_products
 * @property-read int|null $_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ArrivalProducts[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival query()
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereUserId($value)
 * @mixin \Eloquent
 * @property string $comment
 * @property string|null $arrived_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Booking[] $bookings
 * @property-read int|null $bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereArrivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival whereComment($value)
 * @property int $payment_cost
 * @method static \Illuminate\Database\Eloquent\Builder|Arrival wherePaymentCost($value)
 */
class Arrival extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_completed' => 'boolean',
        'count' => 'integer',
        'purchase_price' => 'integer',
    ];

    public function products() {
        return $this->hasMany('App\ArrivalProducts');
    }

    public function _products() {
        return $this->belongsToMany('App\v2\Models\ProductSku', 'arrival_products');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }

    public function bookings() {
        return $this->hasMany('App\v2\Models\Booking');
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($query) {
            $query->comment = $query->comment ?? '';
        });
        static::updating(function ($query) {
            $query->comment = $query->comment ?? '';
        });
    }
}
