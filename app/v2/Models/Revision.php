<?php

namespace App\v2\Models;

use App\Category;
use App\Jobs\Revision\GenerateRevisionExcelFile;
use App\Store;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Bus;

/**
 * App\v2\Models\Revision
 *
 * @property int $id
 * @property int $store_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\RevisionFile[] $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\RevisionProduct[] $products
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

    public function files(): HasMany
    {
        return $this->hasMany(RevisionFile::class, 'revision_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function init(array $payload)
    {
        $revision = $this->create(
            $payload + ['status'  => 'creating']
        );
        $categories = Category::select(['id'])->get();
        $jobs = $categories->map(function ($category) use ($revision) {
            return new GenerateRevisionExcelFile($revision, $category->id);
        });
        Bus::chain($jobs->toArray())->dispatch();
    }
}
