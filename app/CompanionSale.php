<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CompanionSale
 *
 * @property int $id
 * @property int $store_id
 * @property int $companion_id
 * @property int $user_id
 * @property int $discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_consignment
 * @property int $transfer_id
 * @property-read \App\Store $companion
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereCompanionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereIsConsignment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSale whereUserId($value)
 * @mixin \Eloquent
 */
class CompanionSale extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'id' => 'integer',
        'discount' => 'integer',
        'is_consignment' => 'boolean'
    ];

    public function products() {
        return $this->hasMany('App\v2\CompanionSaleProduct', 'companion_sale_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function companion() {
        return $this->belongsTo('App\Store', 'store_id');
    }
}
