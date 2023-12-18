<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FitService
 *
 * @property int $id
 * @property string $name
 * @property int $price
 * @property int|null $visits_count
 * @property int $validity_in_days
 * @property int|null $trainer_amount_per_visit
 * @property int $gym_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FitService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitService query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereTrainerAmountPerVisit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereValidityInDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitService whereVisitsCount($value)
 * @mixin \Eloquent
 * @property-read string $validity_in_days_text
 * @property-read string $visits_count_text
 */
class FitService extends Model
{
    protected $guarded = ['id'];

    public function getVisitsCountTextAttribute(): string
    {
        return $this->pluralizeVisit($this->visits_count);
    }

    public function getValidityInDaysTextAttribute(): string
    {
        $count = $this->validity_in_days;

        if (is_null($count)) {
            return 'Неограниченное количество';
        }

        $count = abs($count);

        // Определяем остаток от деления на 10 и 100
        $lastDigit = $count % 10;
        $lastTwoDigits = $count % 100;

        // Правила для склонения слова "день"
        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 19) {
            // Исключение для чисел от 11 до 19
            return $count . ' дней';
        } elseif ($lastDigit == 1) {
            // Для единственного числа
            return $count . ' день';
        } elseif ($lastDigit >= 2 && $lastDigit <= 4) {
            // Для чисел от 2 до 4
            return $count . ' дня';
        } else {
            // Для всех остальных случаев
            return $count . ' дней';
        }
    }

    private function pluralizeVisit($count): string
    {
        if (is_null($count)) {
            return 'Безлимит';
        }
        // Переводим число в абсолютное значение для случаев, когда $count может быть отрицательным
        $count = abs($count);

        // Определяем остаток от деления на 10 и 100
        $lastDigit = $count % 10;
        $lastTwoDigits = $count % 100;

        // Правила для склонения слова "посещение"
        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 19) {
            // Исключение для чисел от 11 до 19
            return $count . ' посещений';
        } elseif ($lastDigit == 1) {
            // Для единственного числа
            return $count . ' посещение';
        } elseif ($lastDigit >= 2 && $lastDigit <= 4) {
            // Для чисел от 2 до 4
            return $count . ' посещения';
        } else {
            // Для всех остальных случаев
            return $count . ' посещений';
        }
    }
}
