<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\MailingTemplate
 *
 * @property int $id
 * @property string $name
 * @property string $template
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MailingTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailingTemplate extends Model
{
    protected $guarded = [];
}
