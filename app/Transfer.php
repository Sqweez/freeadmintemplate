<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
 */
class Transfer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_accepted' => 'boolean',
        'child_store_id' => 'integer',
        'parent_store_id' => 'integer'
    ];

    const PARTNER_SELLER_ID = 3;


    public function parent_store() {
        return $this->belongsTo('App\Store', 'parent_store_id');
    }

    public function child_store() {
        return $this->belongsTo('App\Store', 'child_store_id');
    }

    public function batches() {
        return $this->hasMany('App\TransferBatch', 'transfer_id');
    }

    public function companionSale() {
        return $this->hasOne('App\CompanionSale', 'transfer_id')->withDefault([
            'is_consignment' => false
        ]);
    }
}
