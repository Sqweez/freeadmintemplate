<?php

namespace App\v2\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\ProductComment
 *
 * @property int $id
 * @property int $product_id
 * @property string $comment
 * @property int|null $client_id
 * @property int|null $user_id
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client|null $client
 * @property-read string $date
 * @property-read ProductComment|null $parent
 * @property-read \App\v2\Models\Product $product
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $fake_name
 * @method static \Illuminate\Database\Eloquent\Builder|ProductComment whereFakeName($value)
 */
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

    public function getCommentName()
    {
        if ($this->fake_name) {
            return $this->fake_name;
        }

        if ($this->client) {
            return $this->client->name;
        }

        return __hardcoded('Гость');
    }
}
