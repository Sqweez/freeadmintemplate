<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AttributeProduct
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_id
 * @property string $attribute_value
 * @property-read \App\Attribute $attribute_name
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereAttributeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeProduct whereProductId($value)
 * @mixin \Eloquent
 */
class AttributeProduct extends Model
{
    protected $guarded = [];

    protected $with = ['attribute_name'];

    public $timestamps = false;

    public function attribute_name() {
        return $this->belongsTo('App\Attribute', 'attribute_id');
    }
}
