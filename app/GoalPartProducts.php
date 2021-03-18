<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GoalPartProducts
 *
 * @property int $id
 * @property int $goal_part_id
 * @property int $product_id
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts whereGoalPartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPartProducts whereProductId($value)
 * @mixin \Eloquent
 * @property-read \App\v2\Models\ProductSku $product
 */
class GoalPartProducts extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku', 'product_id');
    }

}
