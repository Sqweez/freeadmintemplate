<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\CategoryProduct
 *
 * @property int $product_id
 * @property int $category_id
 * @property-read \App\Category $category
 * @property-read \App\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct whereProductId($value)
 * @mixin \Eloquent
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryProduct whereId($value)
 */
class CategoryProduct extends Model
{
    protected $table = 'category_product';

   // protected $with = ['category'];

    protected $guarded = ['id'];

    public $timestamps = false;

    public function product() {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function category() {
        return $this->belongsTo('App\Category')->withDefault([
            'category_name' => 'Неизвестно',
            'id' => -1
        ]);
    }
}
