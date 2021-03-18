<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RatingCriteria
 *
 * @property int $id
 * @property string $criteria
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria query()
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria whereCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RatingCriteria whereId($value)
 * @mixin \Eloquent
 */
class RatingCriteria extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}
