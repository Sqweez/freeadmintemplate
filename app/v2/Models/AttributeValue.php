<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\v2\Models\AttributeValue
 *
 * @property int $id
 * @property int $attribute_id
 * @property string $attribute_value
 * @property-read \App\Attribute $attribute_name
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereAttributeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Product[] $products
 * @property-read int|null $products_count
 */
class AttributeValue extends Model
{
    protected $table = 'attribute_values';

    protected $fillable = ['attribute_id', 'attribute_value'];

    public $timestamps = false;

    protected $hidden = ['pivot'];

    public function attribute_name(): BelongsTo
    {
        return $this->belongsTo('App\Attribute', 'attribute_id');
    }

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'attributable', 'attributable');
    }
}
