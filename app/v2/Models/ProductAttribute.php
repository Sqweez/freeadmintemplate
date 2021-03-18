<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\ProductAttribute
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_value_id
 * @property-read \App\v2\Models\AttributeValue $attribute_value
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereAttributeValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereProductId($value)
 * @mixin \Eloquent
 */
class ProductAttribute extends Model
{
    protected $fillable = ['product_id', 'attribute_value_id'];
    public $timestamps = false;

    public function attribute_value() {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }

}
