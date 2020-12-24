<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Attribute
 *
 * @property int $id
 * @property string $attribute_name
 * @property-read \App\AttributeProduct|null $value
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereAttributeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereId($value)
 * @mixin \Eloquent
 */
class Attribute extends Model
{
    protected $guarded = [];

    public function value() {
        return $this->hasOne('App\AttributeProduct', 'attribute_id');
    }

    public $timestamps = false;
}
