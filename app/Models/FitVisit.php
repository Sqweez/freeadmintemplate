<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FitVisit
 *
 * @property int $id
 * @property int $sale_id
 * @property int $client_id
 * @property int|null $trainer_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FitClient $client
 * @property-read \App\Models\FitServiceSale $sale
 * @property-read \App\Models\FitUser|null $trainer
 * @property-read \App\Models\FitUser $user
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit whereTrainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitVisit whereUserId($value)
 * @mixin \Eloquent
 */
class FitVisit extends Model
{
    protected $guarded = [
        'id'
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(FitUser::class, 'trainer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(FitUser::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(FitClient::class, 'client_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(FitServiceSale::class, 'sale_id');
    }
}
