<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\KaspiEntityProduct
 *
 * @property int $id
 * @property int $kaspi_entity_id
 * @property int $product_id
 * @property int $price
 * @property int $is_visible
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct whereKaspiEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityProduct whereProductId($value)
 * @mixin \Eloquent
 */
class KaspiEntityProduct extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
}
