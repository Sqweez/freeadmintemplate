<?php

namespace App\Models;

use App\Resolvers\Fit\FitClientResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\FitClient
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $birth_date
 * @property string|null $card
 * @property int $gym_id
 * @property int $fit_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FitGym $gym
 * @property-read \App\Models\FitUser $user
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereFitUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $pass
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient wherePass($value)
 * @property int $balance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FitTransaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereBalance($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FitServiceSale[] $activated_services
 * @property-read int|null $activated_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FitServiceSale[] $purchased_services
 * @property-read int|null $purchased_services_count
 * @property string|null $photo
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient wherePhoto($value)
 */
class FitClient extends Model
{
    protected $guarded = ['id'];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(FitGym::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(FitUser::class, 'fit_user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FitTransaction::class, 'client_id');
    }

    public function topUp($payload): Model
    {
        $transaction = $this->transactions()
            ->create([
                'type' => $payload['type'],
                'amount' => $payload['amount'],
                'description' => $payload['description'] ?? null,
                'gym_id' => auth()->user()->gym_id,
                'user_id' => auth()->id(),
            ]);

        $this->increment('balance', $payload['amount']);
        return $transaction;
    }

    public function purchased_services(): HasMany
    {
        return $this->hasMany(FitServiceSale::class, 'client_id');
    }

    public function activated_services(): HasMany
    {
        return $this
            ->hasMany(FitServiceSale::class, 'client_id')
            ->where('is_activated', true);
    }

    public function retrieveClientResource(): FitClient
    {
        return FitClientResolver::i()->resolve($this);
    }
}
