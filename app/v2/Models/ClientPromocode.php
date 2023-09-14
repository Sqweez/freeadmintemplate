<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\v2\Models\ClientPromocode
 *
 * @property int $id
 * @property int $promocode_id
 * @property string|null $card_code
 * @property int|null $client_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode whereCardCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode wherePromocodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPromocode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClientPromocode extends Model
{
    protected $guarded = ['id'];

}
