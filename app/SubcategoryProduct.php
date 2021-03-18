<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SubcategoryProduct
 *
 * @property int $product_id
 * @property int $subcategory_id
 * @property-read Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct whereSubcategoryId($value)
 * @mixin \Eloquent
 * @property-read \App\Subcategory $subcategory
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|SubcategoryProduct whereId($value)
 */
class SubcategoryProduct extends Model {
    protected $table = 'subcategory_product';

    protected $with = ['subcategory'];

    protected $guarded = [];

    public $timestamps = false;

    public function product() {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function subcategory() {
        return $this->belongsTo('App\Subcategory')
            ->withDefault(
                collect(
                    [
                        'subcategory_name' => 'Неизвестно',
                        'id' => -1
                    ]
                )
            );
    }

}
