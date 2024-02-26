<?php

namespace App\v2\Models;

use App\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\Matrix
 *
 * @property int $id
 * @property int $store_id
 * @property array{id: int, count: int} $products
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix query()
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix whereProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matrix whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property array $products
 */
class Matrix extends Model
{
    protected $table = 'matrixes';

    protected $guarded = ['id'];

    protected $casts = [
        'products' => 'array'
    ];

    public function store(): BelongsTo {
        return $this->belongsTo(Store::class);
    }
}
