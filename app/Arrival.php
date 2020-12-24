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
 */
class Arrival extends Model
{
    protected $guarded = [];

    public function products() {
        return $this->hasMany('App\ArrivalProducts');
    }

    public function _products() {
        return $this->belongsToMany('App\Product', 'arrival_products');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }
}
