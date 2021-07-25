<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Education
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $author_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\EducationAttachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\User $author
 * @method static \Illuminate\Database\Eloquent\Builder|Education newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Education newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Education query()
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Education whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Education extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'author_id' => 'integer'
    ];

    public function attachments() {
        return $this->hasMany('App\v2\Models\EducationAttachment', 'education_id');
    }

    public function author() {
        return $this->belongsTo('App\User', 'author_id');
    }
}
