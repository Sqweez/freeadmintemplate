<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Task
 *
 * @property int $id
 * @property int $author_id
 * @property int $user_id
 * @property int $store_id
 * @property string $date_start
 * @property string $date_finish
 * @property bool $is_completion_required
 * @property bool $is_completed
 * @property string $text
 * @property string $title
 * @property string|null $assets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\TaskAttachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\User $author
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAssets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDateFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsCompletionRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUserId($value)
 * @mixin \Eloquent
 */
class Task extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'author_id' => 'integer',
        'store_id' => 'integer',
        'user_id' => 'integer',
        'is_completion_required' => 'boolean',
        'is_completed' => 'boolean'
    ];

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function author() {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function attachments() {
        return $this->hasMany('App\v2\Models\TaskAttachment', 'task_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id')->withDefault([
            'id' => -1,
            'name' => 'Не установлен'
        ]);
    }
}
