<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\TaskAttachment
 *
 * @property int $id
 * @property string $url
 * @property string $name
 * @property int $task_id
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskAttachment whereUrl($value)
 * @mixin \Eloquent
 */
class TaskAttachment extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
