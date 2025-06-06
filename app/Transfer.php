<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Transfer
 *
 * @property int $id
 * @property int $parent_store_id
 * @property int $child_store_id
 * @property int $user_id
 * @property int $is_confirmed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $photos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TransferBatch[] $batches
 * @property-read int|null $batches_count
 * @property-read \App\Store $child_store
 * @property-read \App\Store $parent_store
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereChildStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereIsConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereParentStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereUserId($value)
 * @mixin \Eloquent
 * @property bool $is_accepted
 * @property-read \App\CompanionSale $companionSale
 * @method static \Illuminate\Database\Eloquent\Builder|Transfer whereIsAccepted($value)
 * @property-read \App\User $user
 */
class Transfer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_accepted' => 'boolean',
        'child_store_id' => 'integer',
        'parent_store_id' => 'integer',
        'is_confirmed' => 'boolean'
    ];

    const PARTNER_SELLER_ID = 3;


    public function parent_store(): BelongsTo
    {
        return $this->belongsTo('App\Store', 'parent_store_id')->withTrashed();;
    }

    public function child_store(): BelongsTo
    {
        return $this->belongsTo('App\Store', 'child_store_id')->withTrashed();;
    }

    public function batches(): HasMany
    {
        return $this->hasMany('App\TransferBatch', 'transfer_id');
    }

    public function companionSale(): HasOne
    {
        return $this->hasOne('App\CompanionSale', 'transfer_id')->withDefault([
            'is_consignment' => false
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
