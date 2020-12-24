<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Revision
 *
 * @property int $id
 * @property int $user_id
 * @property int $store_id
 * @property int $is_finished
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\RevisionProducts|null $revision_products
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision query()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereIsFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereUserId($value)
 * @mixin \Eloquent
 */
class Revision extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }

    public function revision_products() {
        return $this->hasOne('App\RevisionProducts');
    }
}
