<?php

namespace App\v2\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    protected $guarded = ['id'];

    protected $appends = [
        'date'
    ];

    public function getDateAttribute(): string {
        return Carbon::parse($this->attributes['created_at'])->format('d.m.Y H:i:s');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('App\v2\Models\ProductComment', 'parent_id');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('App\v2\Models\Product', 'product_id');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('App\Client');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('App\User');
    }
}
