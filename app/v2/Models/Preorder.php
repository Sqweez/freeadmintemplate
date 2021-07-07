<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    const PREORDER_STATUS = [
        -1 => [
            'text' => 'Отменен'
        ],
        0 => [
            'text' => 'Новый'
        ],
        1 => [
            'text' => 'Выполнен'
        ]
    ];

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'amount' => 'integer',
        'payment_type' => 'integer',
        'status' => 'integer',
        'user_id' => 'integer',
        'store_id' => 'integer',
        'client_id' => 'integer',
        'sale_id' => 'integer'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id')->withDefault([
            'name' => 'Неизвестно',
            'id' => -1
        ])->withTrashed();
    }

    public function products() {
        return $this->hasMany('App\v2\Models\PreorderProduct', 'preorder_id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id')
            ->withDefault([
                'name' => 'Iron Addicts - Казахстан',
                'id' => -1,
            ])
            ->withTrashed();
    }

    public function client() {
        return $this->belongsTo('App\Client', 'client_id')->withDefault([
            'client_name' => 'Гость',
            'id' => -1
        ])->withTrashed();
    }

}
