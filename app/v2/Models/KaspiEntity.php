<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\KaspiEntity
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntity whereName($value)
 * @mixin \Eloquent
 * @property string|null $company_name
 * @property string|null $merchant_id
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntity whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiEntity whereMerchantId($value)
 */
class KaspiEntity extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function scopeActive($q)
    {
        return $q->whereNotNull('company_name')
            ->whereNotNull('merchant_id');
    }

}
