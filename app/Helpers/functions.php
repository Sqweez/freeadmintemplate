<?php

use Carbon\Carbon;

if (!function_exists('ucfirstRu')) {
    function ucfirstRu(string $value): string {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}

if (!function_exists('now_format')) {
    function now_format (): string {
        return now()->format('d.m.Y');
    }
}

if (!function_exists('number2string')) {
    function number2string ($number): string
    {
        $f = new NumberFormatter("ru", NumberFormatter::SPELLOUT);
        return $f->format($number);

        // обозначаем словарь в виде статической переменной функции, чтобы
        // при повторном использовании функции его не определять заново

        $number = ceil($number);

        static $dic = array(

            // словарь необходимых чисел
            array(
                -2    => 'две',
                -1    => 'одна',
                1    => 'один',
                2    => 'два',
                3    => 'три',
                4    => 'четыре',
                5    => 'пять',
                6    => 'шесть',
                7    => 'семь',
                8    => 'восемь',
                9    => 'девять',
                10    => 'десять',
                11    => 'одиннадцать',
                12    => 'двенадцать',
                13    => 'тринадцать',
                14    => 'четырнадцать',
                15    => 'пятнадцать',
                16    => 'шестнадцать',
                17    => 'семнадцать',
                18    => 'восемнадцать',
                19    => 'девятнадцать',
                20    => 'двадцать',
                30    => 'тридцать',
                40    => 'сорок',
                50    => 'пятьдесят',
                60    => 'шестьдесят',
                70    => 'семьдесят',
                80    => 'восемьдесят',
                90    => 'девяносто',
                100    => 'сто',
                200    => 'двести',
                300    => 'триста',
                400    => 'четыреста',
                500    => 'пятьсот',
                600    => 'шестьсот',
                700    => 'семьсот',
                800    => 'восемьсот',
                900    => 'девятьсот'
            ),

            // словарь порядков со склонениями для плюрализации
            array(
                array('', '', ''),
                array('тысяча', 'тысячи', 'тысяч'),
                array('миллион', 'миллиона', 'миллионов'),
                array('миллиард', 'миллиарда', 'миллиардов'),
                array('триллион', 'триллиона', 'триллионов'),
                array('квадриллион', 'квадриллиона', 'квадриллионов'),
                // квинтиллион, секстиллион и т.д.
            ),

            // карта плюрализации
            array(
                2, 0, 1, 1, 1, 2
            )
        );

        // обозначаем переменную в которую будем писать сгенерированный текст
        $string = array();

        // дополняем число нулями слева до количества цифр кратного трем,
        // например 1234, преобразуется в 001234
        $number = str_pad($number, ceil(strlen($number) / 3) * 3, 0, STR_PAD_LEFT);

        // разбиваем число на части из 3 цифр (порядки) и инвертируем порядок частей,
        // т.к. мы не знаем максимальный порядок числа и будем бежать снизу
        // единицы, тысячи, миллионы и т.д.
        $parts = array_reverse(str_split($number, 3));

        try {
            foreach ($parts as $i => $part) {

                // если часть не равна нулю, нам надо преобразовать ее в текст
                if ($part > 0) {

                    // обозначаем переменную в которую будем писать составные числа для текущей части
                    $digits = array();

                    // если число треххзначное, запоминаем количество сотен
                    if ($part > 99) {
                        $digits[] = floor($part / 100) * 100;
                    }

                    // если последние 2 цифры не равны нулю, продолжаем искать составные числа
                    // (данный блок прокомментирую при необходимости)
                    if ($mod1 = $part % 100) {
                        $mod2 = $part % 10;
                        $flag = $i == 1 && $mod1 != 11 && $mod1 != 12 && $mod2 < 3 ? -1 : 1;
                        if ($mod1 < 20 || !$mod2) {
                            $digits[] = $flag * $mod1;
                        } else {
                            $digits[] = floor($mod1 / 10) * 10;
                            $digits[] = $flag * $mod2;
                        }
                    }

                    // берем последнее составное число, для плюрализации
                    $last = abs(end($digits));

                    // преобразуем все составные числа в слова
                    foreach ($digits as $j => $digit) {
                        $digits[$j] = $dic[0][$digit];
                    }

                    // добавляем обозначение порядка или валюту
                    $digits[] = $dic[1][$i][(($last %= 100) > 4 && $last < 20) ? 2 : $dic[2][min($last % 10, 5)]];

                    // объединяем составные числа в единый текст и добавляем в переменную, которую вернет функция
                    array_unshift($string, join(' ', $digits));
                }
            }

            // преобразуем переменную в текст и возвращаем из функции, ура!
            return join(' ', $string);
        } catch (\Exception $exception) {
            return $number;
        }

        // бежим по каждой части

    }
}

if (!function_exists('__hardcoded')) {
    function __hardcoded($value) {
        return $value;
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime ($date = null): ?string {
        return is_null($date) ? null : Carbon::parse($date)->format('d.m.Y H:i:s');
    }
}

if (!function_exists('format_date')) {
    function format_date ($date = null): ?string {
        return is_null($date) ? null : Carbon::parse($date)->format('d.m.Y');
    }
}
if (!function_exists('generate_number')) {
    function generate_number($length): string {
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }
}

if (!function_exists('unmask_phone')) {
    function unmask_phone ($phone) {
        return str_replace(['(', ')', '-', ' '], '', $phone);
    }
}

if (!function_exists('_divide')) {
    function _divide ($args) {
        try {
            return $args;
        } catch (Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('_transliterate')) {
    function _transliterate($text): string
    {
        $translit = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'c', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
        );

        return strtr($text, $translit);
    }
}

if (!function_exists('price_format')) {
    function price_format($value, $currencyCode = '₸', $decimals = 0): string
    {
        return number_format($value, $decimals, ".", " ") . $currencyCode;
        $fmt = new NumberFormatter( 'ru_RU', NumberFormatter::CURRENCY );
        return $fmt->formatCurrency($value, $currencyCode);
    }
}
