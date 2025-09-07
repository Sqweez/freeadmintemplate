<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

/**
 * App\v2\Models\KaspiEntity
 *
 * @property int $id
 * @property string $name
 * @method static Builder|KaspiEntity newModelQuery()
 * @method static Builder|KaspiEntity newQuery()
 * @method static Builder|KaspiEntity query()
 * @method static Builder|KaspiEntity whereId($value)
 * @method static Builder|KaspiEntity whereName($value)
 * @mixin \Eloquent
 * @property string|null $company_name
 * @property string|null $merchant_id
 * @method static Builder|KaspiEntity whereCompanyName($value)
 * @method static Builder|KaspiEntity whereMerchantId($value)
 */
class KaspiEntity extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function scopeActive($q)
    {
        return $q->whereNotNull('company_name')->whereNotNull('merchant_id');
    }

    public function stores(): HasMany
    {
        return $this->hasMany(KaspiEntityStore::class, 'kaspi_entity_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(KaspiEntityProduct::class, 'kaspi_entity_id', 'id');
    }

    public function setKaspiTokenAttribute($value): void
    {
        $this->attributes['kaspi_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getKaspiTokenAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // если не удалось расшифровать, вернуть как есть
        }
    }
}
