<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\KaspiEntityStore
 *
 * @property int $id
 * @property int $kaspi_entity_id
 * @property int $store_id
 * @property int $kaspi_store_id
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityStore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityStore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityStore query()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityStore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityStore whereKaspiEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityStore whereKaspiStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntityStore whereStoreId($value)
 * @mixin \Eloquent
 */
class KaspiEntityStore extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;


}
