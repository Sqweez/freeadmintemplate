<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\v2\Models\Mailing
 *
 * @property int $id
 * @property int $user_id
 * @property string $mailing_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\MailingRecepient[] $recipients
 * @property-read int|null $recipients_count
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereMailingText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereUserId($value)
 * @mixin \Eloquent
 */
class Mailing extends Model
{
    protected $guarded = [];

    public function recipients(): HasMany {
        return $this->hasMany(MailingRecepient::class, 'mailing_id');
    }
}
