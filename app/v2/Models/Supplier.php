<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Supplier
 *
 * @property int $id
 * @property string $supplier_name
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereUserId($value)
 * @mixin \Eloquent
 */
class Supplier extends Model
{
    protected $fillable = ['supplier_name', 'user_id'];

    protected $casts = [
        'user_id' => 'integer',
        'id' => 'integer'
    ];

    public $timestamps = false;

    public function products() {
        return $this->hasMany('App\v2\Models\Product', 'supplier_id');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
