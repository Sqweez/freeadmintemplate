<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\OrderMessage
 *
 * @property int $id
 * @property string $chat_id
 * @property string $message
 * @property int $is_delivered
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereIsDelivered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $order_id
 * @property-read \App\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMessage whereOrderId($value)
 */
class OrderMessage extends Model
{
    protected $fillable = ['chat_id', 'order_id', 'is_delivered'];

    public function order() {
        return $this->belongsTo('App\Order');
    }
}
