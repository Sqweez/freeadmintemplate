<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\MailingRecepient
 *
 * @property int $id
 * @property int $client_id
 * @property int $mailing_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient whereMailingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingRecepient whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailingRecepient extends Model
{
    protected $guarded = [];
}
