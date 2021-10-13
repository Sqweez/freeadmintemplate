<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\BrandMotivation
 *
 * @property int $id
 * @property array $amount
 * @property array $brands
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation whereBrands($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandMotivation whereId($value)
 * @mixin \Eloquent
 */
class BrandMotivation extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'amount' => 'array',
        'brands' => 'array'
    ];

}
