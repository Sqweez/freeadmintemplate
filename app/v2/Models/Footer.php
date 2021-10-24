<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Footer
 *
 * @property int $id
 * @property array|null $addresses
 * @property array|null $contacts
 * @method static \Illuminate\Database\Eloquent\Builder|Footer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Footer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Footer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereAddresses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereId($value)
 * @mixin \Eloquent
 */
class Footer extends Model
{
    protected $table = 'footer';
    public $timestamps = false;
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'addresses' => 'array',
        'contacts' => 'array'
    ];
}
