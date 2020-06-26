<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Goal extends Model
{
    protected $guarded = [];

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
