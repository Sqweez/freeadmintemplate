<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ExcelService;
use App\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;

class WaybillController extends Controller
{
    public function transferWaybill(Request $request) {
        $cart = $request->get('cart');
        $parent_store = $request->get('parent_store');
        $child_store = $request->get('child_store');
        $excelService = new ExcelService();
        $excelTemplate = $excelService->loadFile('waybill_transfer_template', 'xls');
        $excelSheet = $excelTemplate->getActiveSheet();

        $INITIAL_PRODUCT_ROW = 24;
        $PRODUCT_COUNT = count($cart);
        $TOTAL_COST = $this->getTotalCost($cart);
        $TOTAL_COUNT = $this->getTotalCount($cart);

        $parent_city = Store::find($parent_store)->city;
        $child_city = Store::find($child_store)->city;

        $parent_store_name = 'IRON ADDICTS, ' . $parent_city;
        $child_store_name = 'IRON ADDICTS, '. $child_city;

        $excelSheet->setCellValue('L18', $parent_store_name);
        $excelSheet->setCellValue('L19', $child_store_name);

        foreach ($cart as $key => $item) {
            $currentIndex = $key + $INITIAL_PRODUCT_ROW;
            try {
                $excelSheet->insertNewRowBefore($currentIndex, 1);
            } catch (Exception $e) {
            }
            try {
                $excelSheet->mergeCells("A" . $currentIndex . ":B" . $currentIndex);
                $excelSheet->mergeCells("C" . $currentIndex . ":N" . $currentIndex);
                $excelSheet->mergeCells("O" . $currentIndex . ":S" . $currentIndex);
                $excelSheet->mergeCells("T" . $currentIndex . ":V" . $currentIndex);
                $excelSheet->mergeCells("W" . $currentIndex . ":AA" . $currentIndex);
                $excelSheet->mergeCells("AB" . $currentIndex . ":AE" . $currentIndex);
                $excelSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $excelSheet->mergeCells("AF" . $currentIndex . ":AK" . $currentIndex);
                $excelSheet->mergeCells("AL" . $currentIndex . ":AQ" . $currentIndex);
                $excelSheet->mergeCells("AR" . $currentIndex . ":AW" . $currentIndex);
            }
            catch (\Exception $e) {

            }

            $excelSheet->setCellValue('A' . ($currentIndex), $key + 1);
            $excelSheet->setCellValue('C' . ($currentIndex), $this->getProductName($item));
            $excelSheet->setCellValue('T' . ($currentIndex), "шт.");
            $excelSheet->setCellValue('W' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('AB' . ($currentIndex), $item['count']);
            $excelSheet->setCellValue('AF' . ($currentIndex), mb_convert_case($item['product_price'], MB_CASE_TITLE, 'UTF-8'));
            $excelSheet->setCellValue('AL' . ($currentIndex), mb_convert_case(intval($item['product_price']) * intval($item['count']), MB_CASE_TITLE, 'UTF-8'));
            $excelSheet->setCellValue('AR' . ($currentIndex), mb_convert_case(intval($item['product_price']) * intval($item['count']), MB_CASE_TITLE, 'UTF-8'));
        }


        $excelSheet->setCellValue('AR' . ($INITIAL_PRODUCT_ROW + $PRODUCT_COUNT), $TOTAL_COST);
        $excelSheet->setCellValue('AE' . (26 + $PRODUCT_COUNT), $this->number2string($TOTAL_COST));
        $excelSheet->setCellValue('N' . (26 + $PRODUCT_COUNT), $this->number2string($TOTAL_COUNT));

        $excelWriter = new Xlsx($excelTemplate);

        $fileName = "Перемещение_" . Carbon::today()->toDateString() . "_" . $parent_city . '-' . $child_city . "_" . Str::random(10) . '.xlsx';
        $fullPath = 'storage/excel/waybills/transfers/' . $fileName;

        $excelWriter->save($fullPath);

        return response()->json([
            'path' => $fullPath
        ]);
    }

    private function getTotalCost($cart) {
        return array_reduce($cart, function ($a, $c) {
            return $c['product_price'] * $c['count'] + $a;
        }, 0);
    }

    private function getProductName($item) {
        $attributeValues = join(' | ' , array_map(function ($i) {
            return $i['attribute_value'];
        }, $item['attributes']));
        return $item['manufacturer'] . ' ' . $item['product_name'] . ' ' . $attributeValues;
    }

    public function number2string($number) {

        // обозначаем словарь в виде статической переменной функции, чтобы
        // при повторном использовании функции его не определять заново
        static $dic = array(

            // словарь необходимых чисел
            array(
                -2	=> 'две',
                -1	=> 'одна',
                1	=> 'один',
                2	=> 'два',
                3	=> 'три',
                4	=> 'четыре',
                5	=> 'пять',
                6	=> 'шесть',
                7	=> 'семь',
                8	=> 'восемь',
                9	=> 'девять',
                10	=> 'десять',
                11	=> 'одиннадцать',
                12	=> 'двенадцать',
                13	=> 'тринадцать',
                14	=> 'четырнадцать' ,
                15	=> 'пятнадцать',
                16	=> 'шестнадцать',
                17	=> 'семнадцать',
                18	=> 'восемнадцать',
                19	=> 'девятнадцать',
                20	=> 'двадцать',
                30	=> 'тридцать',
                40	=> 'сорок',
                50	=> 'пятьдесят',
                60	=> 'шестьдесят',
                70	=> 'семьдесят',
                80	=> 'восемьдесят',
                90	=> 'девяносто',
                100	=> 'сто',
                200	=> 'двести',
                300	=> 'триста',
                400	=> 'четыреста',
                500	=> 'пятьсот',
                600	=> 'шестьсот',
                700	=> 'семьсот',
                800	=> 'восемьсот',
                900	=> 'девятьсот'
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
        $number = str_pad($number, ceil(strlen($number)/3)*3, 0, STR_PAD_LEFT);

        // разбиваем число на части из 3 цифр (порядки) и инвертируем порядок частей,
        // т.к. мы не знаем максимальный порядок числа и будем бежать снизу
        // единицы, тысячи, миллионы и т.д.
        $parts = array_reverse(str_split($number,3));

        // бежим по каждой части
        foreach($parts as $i=>$part) {

            // если часть не равна нулю, нам надо преобразовать ее в текст
            if($part>0) {

                // обозначаем переменную в которую будем писать составные числа для текущей части
                $digits = array();

                // если число треххзначное, запоминаем количество сотен
                if($part>99) {
                    $digits[] = floor($part/100)*100;
                }

                // если последние 2 цифры не равны нулю, продолжаем искать составные числа
                // (данный блок прокомментирую при необходимости)
                if($mod1=$part%100) {
                    $mod2 = $part%10;
                    $flag = $i==1 && $mod1!=11 && $mod1!=12 && $mod2<3 ? -1 : 1;
                    if($mod1<20 || !$mod2) {
                        $digits[] = $flag*$mod1;
                    } else {
                        $digits[] = floor($mod1/10)*10;
                        $digits[] = $flag*$mod2;
                    }
                }

                // берем последнее составное число, для плюрализации
                $last = abs(end($digits));

                // преобразуем все составные числа в слова
                foreach($digits as $j=>$digit) {
                    $digits[$j] = $dic[0][$digit];
                }

                // добавляем обозначение порядка или валюту
                $digits[] = $dic[1][$i][(($last%=100)>4 && $last<20) ? 2 : $dic[2][min($last%10,5)]];

                // объединяем составные числа в единый текст и добавляем в переменную, которую вернет функция
                array_unshift($string, join(' ', $digits));
            }
        }

        // преобразуем переменную в текст и возвращаем из функции, ура!
        return join(' ', $string);
    }

    public function getRowHeight($name, $initialFormat = 36) {
        $length = strlen($name);
        return ceil($length / $initialFormat);
    }

    private function mergeCells($currentIndex) {

    }

    private function getTotalCount($cart) {
        return array_reduce($cart, function ($a, $c) {
            return $c['count'] + $a;
        }, 0);
    }

}