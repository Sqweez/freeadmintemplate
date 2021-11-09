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
 */
class Promocode extends Model
{
    protected $fillable = ['client_id', 'promocode', 'discount', 'is_active', 'id'];

    public function partner() {
        return $this->belongsTo('App\Client', 'client_id')->withTrashed()->withDefault([
            'client_name' => 'Удален',
            'id' => -1,
        ]);
    }

    public function scopeOfPartner($query, $id) {
        $query->where('client_id', $id);
    }
}
