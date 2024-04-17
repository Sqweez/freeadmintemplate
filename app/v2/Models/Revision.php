<?php

namespace App\v2\Models;

use App\Store;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\v2\Models\Revision
 *
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\v2\Models\RevisionFile[] $files
 * @property-read int|null $files_count
 * @property-read Collection|\App\v2\Models\RevisionProduct[] $products
 * @property-read int|null $products_count
 * @property-read Store $store
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision query()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereUserId($value)
 * @mixin \Eloquent
 * @property string $status
 * @method static \Illuminate\Database\Eloquent\Builder|Revision whereStatus($value)
 * @property-read string $percentage_completed
 * @property-read string $status_text
 * @property-read mixed $uploaded_files_count
 * @property-read Collection|\App\v2\Models\RevisionFile[] $filesUploaded
 * @property-read int|null $files_uploaded_count
 * @property-read mixed $actual_total_price
 * @property-read mixed $expected_total_price
 */
class Revision extends Model
{
    protected $table = 'v2_revisions';

    protected $guarded = [
        'id'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(RevisionProduct::class, 'revision_id');
    }

    public function getStatusTextAttribute(): string
    {
        switch ($this->status) {
            case 'created':
                return 'Создана';
            case 'creating':
                return 'Создается';
            default:
                return 'В процессе';
        }
    }

    public function files(): HasMany
    {
        return $this->hasMany(RevisionFile::class, 'revision_id');
    }

    public function filesUploaded()
    {
        return $this->hasMany(RevisionFile::class, 'revision_id')->whereNotNull('uploaded_file_path');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id')->select(['id', 'name']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->select(['id', 'name', 'store_id']);
    }

    public function getPercentageCompletedAttribute(): string
    {
        if (!$this->files_count) {
            return 0 . "%";
        }
        return number_format(
            (100 * $this->files_uploaded_count / $this->files_count),
            2
        ) . "%";
    }

    public function getExpectedTotalPriceAttribute()
    {
        return $this->products->reduce(function ($a, RevisionProduct $c) {
            return $a + $c->getExpectedCountPriceTotalAttribute();
        }, 0);
    }

    public function getActualTotalPriceAttribute()
    {
        return $this->products->reduce(function ($a, RevisionProduct $c) {
            return $a + $c->getActualCountPriceTotalAttribute();
        }, 0);
    }
}
