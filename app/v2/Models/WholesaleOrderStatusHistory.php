<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\WholesaleOrderStatusHistory
 *
 * @property int $id
 * @property int $wholesale_order_id
 * @property int $wholesale_status_id
 * @property int $user_id
 * @property string $changed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory whereChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory whereWholesaleOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatusHistory whereWholesaleStatusId($value)
 * @mixin \Eloquent
 */
class WholesaleOrderStatusHistory extends Model
{
    protected $guarded = ['id'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(WholesaleOrder::class, 'wholesale_status_id');
    }

}
