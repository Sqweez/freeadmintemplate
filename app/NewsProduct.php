<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsProduct extends Model
{
    protected $table = 'news_products';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'news_id' => 'integer'
    ];

    public function news() {
        return $this->belongsTo('App\v2\Models\News', 'news_id');
    }

    public function product() {
        return $this->belongsTo('App\v2\Models\Product', 'product_id');
    }
}
