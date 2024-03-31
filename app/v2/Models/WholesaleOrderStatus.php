<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\WholesaleOrderStatus
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read WholesaleOrderStatus $status
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WholesaleOrderStatus extends Model
{
    protected $guarded = ['id'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(WholesaleOrderStatus::class, 'wholesale_status_id');
    }
}
