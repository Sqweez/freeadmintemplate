<?php

namespace App;

use App\Models\v2\BankAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\LegalEntity
 *
 * @property int $id
 * @property string $name
 * @property string $iin
 * @property string $address
 * @property string $iik
 * @property string $bik
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereBik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereIik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereIin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $boss
 * @property-read \Illuminate\Database\Eloquent\Collection|BankAccount[] $bank_accounts
 * @property-read int|null $bank_accounts_count
 * @method static \Illuminate\Database\Eloquent\Builder|LegalEntity whereBoss($value)
 */
class LegalEntity extends Model
{
    protected $guarded = ['id'];

    public function bank_accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'legal_entity_id');
    }
}
