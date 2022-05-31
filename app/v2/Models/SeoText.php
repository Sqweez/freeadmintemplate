<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoText extends Model
{
    protected $guarded = ['id'];

    public function entity(): MorphTo {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }
}
