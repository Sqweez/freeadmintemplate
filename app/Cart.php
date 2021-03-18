<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Cart
 *
 * @property int $id
 * @property string $user_token
 * @property string $type
 * @property int $store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CartProduct[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart ofUser($token)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserToken($value)
 * @mixin \Eloquent
 */
class Cart extends Model
{
    protected $guarded = [];

    public function products() {
        return $this->hasMany('App\CartProduct', 'cart_id');
    }

    public function scopeOfUser($q, $token) {
        return $q->where('user_token', $token);
    }

}
