<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ManufacturerProducts
 *
 * @property int $id
 * @property int $product_id
 * @property int $manufacturer_id
 * @property-read \App\Manufacturer|null $manufacturer
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts whereManufacturerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufacturerProducts whereProductId($value)
 * @mixin \Eloquent
 */
class ManufacturerProducts extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $with = ['manufacturer'];

    public function manufacturer() {
        return $this->belongsTo('App\Manufacturer')
            ->withDefault([
                'manufacturer_name' => 'Неизвестно',
                'id' => -1
            ]);
    }
}
