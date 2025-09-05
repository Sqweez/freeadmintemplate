<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
