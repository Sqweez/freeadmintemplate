<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\EducationAttachment
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $education_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereEducationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EducationAttachment whereUrl($value)
 * @mixin \Eloquent
 */
class EducationAttachment extends Model
{
    protected $guarded = [
        'id'
    ];
}
