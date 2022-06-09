<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\v2\Models\SeoText
 *
 * @property int $id
 * @property string|null $content
 * @property string $entity_type
 * @property int $entity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $entity
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText query()
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SeoText whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SeoText extends Model
{
    protected $guarded = ['id'];

    public function entity(): MorphTo {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }
}
