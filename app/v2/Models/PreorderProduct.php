<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\PreorderProduct
 *
 * @property int $id
 * @property int $preorder_id
 * @property int $product_id
 * @property-read \App\v2\Models\ProductSku $product
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct wherePreorderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreorderProduct whereProductId($value)
 * @mixin \Eloquent
 */
class PreorderProduct extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku', 'product_id')->withDefault([
            'product_name' => 'Неизвестно',
            'attributes' => [],
            'manufacturer' => collect([])
        ])->withTrashed();
    }
}
