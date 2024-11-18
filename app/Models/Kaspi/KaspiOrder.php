<?php

namespace App\Models\Kaspi;

use App\Service\Kaspi\KaspiOrdersApiService;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Kaspi\KaspiOrder
 *
 * @property int $id
 * @property string $kaspi_id
 * @property array $attributes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder whereKaspiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KaspiOrder whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KaspiOrder extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'attributes' => 'json'
    ];

    public function getOrderEntities(): ?array
    {
        $kaspiService = new KaspiOrdersApiService();
        return $kaspiService->getOrderEntries($this->kaspi_id);
    }
}
