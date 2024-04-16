<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\RevisionFile
 *
 * @property int $id
 * @property int $revision_id
 * @property int $category_id
 * @property string $original_file_path
 * @property string|null $uploaded_file_path
 * @property string|null $uploaded_at
 * @property string|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereOriginalFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereRevisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionFile whereUploadedFilePath($value)
 * @mixin \Eloquent
 * @property-read string|null $original_file_full_path
 * @property-read string|null $uploaded_file_full_path
 * @property-read Revision $revision
 */
class RevisionFile extends Model
{
    protected $guarded = [
        'id'
    ];

    public function revision(): BelongsTo
    {
        return $this->belongsTo(Revision::class, 'revision_id');
    }

    public function getOriginalFileFullPathAttribute(): ?string
    {
        if ($this->original_file_path) {
            return \Storage::url($this->original_file_path);
        }
        return null;
    }

    public function getUploadedFileFullPathAttribute(): ?string
    {
        if ($this->uploaded_file_path) {
            return \Storage::url($this->uploaded_file_path);
        }
        return null;
    }
}
