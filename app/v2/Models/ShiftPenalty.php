<?php

namespace App\v2\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\ShiftPenalty
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount Отрицательные значения для штрафов, положительные для премий
 * @property int $author_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $author
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftPenalty whereUserId($value)
 * @mixin \Eloquent
 * @property-read mixed $date
 */
class ShiftPenalty extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'user_id' => 'integer',
        'amount' => 'integer',
        'author_id' => 'integer'
    ];

    public function user() {
        return $this->belongsTo(User::class)->select(['id', 'name']);
    }

    public function author() {
        return $this->belongsTo(User::class, 'author_id')->select(['id', 'name']);
    }

    public function getDateAttribute() {
        return Carbon::parse($this->created_at)->format('d.m.Y');
    }
}
