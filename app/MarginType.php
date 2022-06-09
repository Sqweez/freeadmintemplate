<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MarginType
 *
 * @property int $id
 * @property string $title
 * @property array|null $partner_cashback_rules
 * @property array|null $salary_rules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType query()
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType wherePartnerCashbackRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType whereSalaryRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarginType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MarginType extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'salary_rules' => 'array',
        'partner_cashback_rules' => 'array'
    ];
}
