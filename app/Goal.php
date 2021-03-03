<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * App\Goal
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GoalPart[] $parts
 * @property-read int|null $parts_count
 * @method static \Illuminate\Database\Eloquent\Builder|Goal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $mobile_image
 * @method static \Illuminate\Database\Eloquent\Builder|Goal whereMobileImage($value)
 */
class Goal extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer'
    ];

    public function parts() {
        return $this->hasMany('App\GoalPart', 'goal_id');
    }

    protected static function boot() {
        parent::boot();

        static::creating(function ($query) {
            $query->image = $query->image ?? "";
            $query->slug = Str::slug($query->name);
        });

        static::updating(function ($query) {
            $query->image = $query->image ?? "";
            $query->slug = Str::slug($query->name);
        });

        static::deleting(function ($query) {
            Storage::delete('public/' . $query->image);
            $goalParts = GoalPart::where('goal_id', $query->id)->get();
            collect($goalParts)->map(function ($i) {
                GoalPartProducts::where('goal_part_id', $i['id'])->delete();
                $i->delete();
            });

        });
    }
}
