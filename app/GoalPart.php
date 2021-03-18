<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GoalPart
 *
 * @property int $id
 * @property int $goal_id
 * @property int $category_id
 * @property int|null $subcategory_id
 * @property string $name
 * @property string|null $description
 * @property-read \App\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GoalPartProducts[] $products
 * @property-read int|null $products_count
 * @property-read \App\Subcategory|null $subcategory
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereGoalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereSubcategoryId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|GoalPart whereProducts($value)
 */
class GoalPart extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'goal_id' => 'integer',
        'products' => 'array'
    ];


    public function category() {
        return $this->belongsTo('App\Category')->withDefault([
            'category_name' => ''
        ]);
    }

    public function subcategory() {
        return $this->belongsTo('App\Subcategory')->withDefault([
            'subcategory_name' => ''
        ]);
    }

}
