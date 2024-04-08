<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Currency
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $unicode_symbol
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUnicodeSymbol($value)
 * @mixin \Eloquent
 */
class Currency extends Model
{
    public $timestamps = false;
    protected $guarded = [
        'id'
    ];

    public function getDeclension($amount): string
    {
        if ($this->id === 1) {
            return 'тенге';
        }
        if ($this->id === 2) {
            return $this->rubleDeclension($amount);
        }

        return 'тенге';
    }

    public function rubleDeclension($number): string
    {
        $number = abs($number) % 100;
        $last_digit = $number % 10;

        if ($number > 10 && $number < 20) return "рублей";
        if ($last_digit > 1 && $last_digit < 5) return "рубля";
        if ($last_digit == 1) return "рубль";
        return "рублей";
    }
}
