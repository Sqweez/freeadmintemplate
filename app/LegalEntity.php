<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LegalEntity
 *
 * @property int $id
 * @property string $name
 * @property string $iin
 * @property string $address
 * @property string $iik
 * @property string $bik
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 */
class LegalEntity extends Model
{
    protected $guarded = [];
}
