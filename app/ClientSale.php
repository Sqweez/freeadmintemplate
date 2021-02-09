<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ClientSale
 *
 * @property int $id
 * @property int $client_id
 * @property int $sale_id
 * @property int $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientSale whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Sale $sale
 */
class ClientSale extends Model
{
    protected $guarded = [];

    public function sale() {
        return $this->belongsTo('App\Sale');
    }
}
