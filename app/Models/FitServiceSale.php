<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\FitServiceSale
 *
 * @property int $id
 * @property int $service_id
 * @property int $user_id
 * @property int $client_id
 * @property int $price
 * @property int $payment_type
 * @property-read int|null $visits_count
 * @property int $is_activated
 * @property int $transaction_id
 * @property string|null $description
 * @property int $gym_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FitClient $client
 * @property-read \App\Models\FitService $service
 * @property-read \App\Models\FitTransaction $transaction
 * @property-read \App\Models\FitUser $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FitVisit[] $visits
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereIsActivated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereVisitsCount($value)
 * @mixin \Eloquent
 * @property string|null $activated_at
 * @property string|null $valid_until
 * @property int|null $validity_in_days
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitServiceSale whereValidityInDays($value)
 */
class FitServiceSale extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'is_activated' => 'boolean'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(FitService::class, 'service_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(FitUser::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(FitClient::class, 'client_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FitTransaction::class, 'transaction_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(FitVisit::class, 'sale_id');
    }

    public function activate()
    {
        $this->update([
            'activated_at' => now(),
            'is_activated' => true,
            'valid_until' => $this->validity_in_days ? now()->addDays($this->validity_in_days - 1) : now()->addYear()
        ]);
    }

    public function getValidUntilAttributeText(): ?string
    {
        if (!$this->is_activated || !$this->activated_at) {
            return 'Не активирован';
        }
        return format_date($this->valid_until);
    }

    public function getVisitsRemainingAttributeText()
    {
        if (is_null($this->visits_count)) {
            return 'Неограниченно';
        }

        return $this->visits_count - $this->visits->count();
    }


    public function getCanBeUsed(): bool
    {
        if (!$this->is_activated) {
            return true;
        }

        if (!is_null($this->visits_count) && $this->visits_count - $this->visits->count() <= 0) {
            return false;
        }

        return !Carbon::parse($this->valid_until)->lte(today());
    }
}
