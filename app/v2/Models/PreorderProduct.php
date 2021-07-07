<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

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
