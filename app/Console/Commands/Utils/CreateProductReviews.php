<?php

namespace App\Console\Commands\Utils;

use App\v2\Models\ProductComment;
use App\v2\Models\ProductSku;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreateProductReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $realPath = 'Console/Commands/Utils/assets/reviews.json';
        $jsonFilePath = app_path($realPath);
        $jsonContent = file_get_contents($jsonFilePath);
        $products = collect(json_decode($jsonContent, true));
        $products = $products
            ->groupBy('id')
            ->each(function ($items, $id) {
                $productSkuId = $id;
                $productId = ProductSku::find($productSkuId)->product_id;
                ProductComment::where('product_id', $productId)->delete();
                collect($items)
                    ->each(function ($item) use ($productId) {
                        $this->line($item['review']);
                        ProductComment::create([
                            'product_id' => $productId,
                            'comment' => $item['review'],
                            'user_id' => __hardcoded(11),
                            'fake_name' => $this->getFakeName(),
                            'created_at' => $this->getDate(),
                        ]);
                    });
            });
        /*$lastCommentId = __hardcoded(7194);

        ProductComment::where('id', '>=', $lastCommentId)
            ->delete();

        ProductComment::whereNotNull('fake_name')
            ->get()
            ->each(function (ProductComment $productComment) {
                $productComment->update([
                    'fake_name' => $this->getFakeName()
                ]);
            });*/

        /*foreach ($products as $product) {
            $productSkuId = $product['id'];
            $review = $product['review'];
            $productId = ProductSku::find($productSkuId)->product_id;
            ProductComment::create([
                'product_id' => $productId,
                'comment' => $review,
                'user_id' => __hardcoded(11),
                'fake_name' => $this->getFakeName(),
                'created_at' => $this->getDate(),
            ]);
        };*/
    }

    private function getFakeName(): string
    {
        $names = [
            [
                "Александр",
                "Иван",
                "Михаил",
                "Артем",
                "Дмитрий",
                "Сергей",
                "Максим",
                "Николай",
                "Андрей",
                "Павел",
                "Антон",
                "Григорий",
                "Евгений",
                "Илья",
                "Константин",
                "Федор",
                "Тимур",
                "Роман",
                "Ярослав",
                "Владислав",
                "Виктор",
                "Семен",
                "Станислав",
                "Марк",
                "Филипп",
                "Антонин",
                "Матвей",
                "Егор",
                "Леонид",
                "Даниил",
            ],
            [
                "Анна",
                "Екатерина",
                "Мария",
                "Ирина",
                "Ольга",
                "Елена",
                "Наталья",
                "Светлана",
                "Татьяна",
                "Виктория",
                "Людмила",
                "Анастасия",
                "Алиса",
                "Дарья",
                "Ева",
                "Зоя",
                "Лариса",
                "Милана",
                "Надежда",
                "Юлия",
                "Ксения",
                "Елизавета",
                "Алина",
                "София",
                "Василиса",
                "Алёна",
                "Арина",
                "Ангелина",
                "Инна",
                "Альбина",
            ],
            [
                "Абай",
                "Бекзат",
                "Данияр",
                "Ермек",
                "Жандар",
                "Зейнеке",
                "Исмаил",
                "Куаныш",
                "Мадияр",
                "Нурсултан",
                "Руслан",
                "Сабит",
                "Темирлан",
                "Улан",
                "Хасан",
                "Али",
                "Бауыржан",
                "Гани",
                "Досжан",
                "Ербол",
                "Заур",
                "Ислам",
                "Кайрат",
                "Марат",
                "Нурлан",
                "Рауан",
                "Серик",
                "Толеген",
                "Усен",
                "Хусейн",
            ],
            [
                "Айгуль",
                "Ботагоз",
                "Гульмира",
                "Динара",
                "Жанар",
                "Зарина",
                "Индира",
                "Кульжан",
                "Мадина",
                "Нургуль",
                "Роза",
                "Сауле",
                "Тамара",
                "Улжан",
                "Халима",
                "Айым",
                "Балжан",
                "Гульжан",
                "Дильназ",
                "Жанель",
                "Зульфия",
                "Ирена",
                "Камшат",
                "Маржан",
                "Нуржан",
                "Римма",
                "Светлана",
                "Толганай",
                "Умида",
                "Хайгуль",
            ]
        ];
        $russianSurnameStartLetters = [
            "А",
            "Б",
            "В",
            "Г",
            "Д",
            "Е",
            "Ё",
            "Ж",
            "З",
            "И",
            "К",
            "Л",
            "М",
            "Н",
            "О",
            "П",
            "Р",
            "С",
            "Т",
            "У",
            "Ф",
            "Х",
            "Ц",
            "Ч",
            "Ш",
            "Щ",
            "Э",
            "Ю",
            "Я",
        ];

        $rand = mt_rand(0, 3);
        $randName = mt_rand(0, 29);
        $randSurname = mt_rand(0, count($russianSurnameStartLetters) - 1);

        return sprintf("%s %s.", $names[$rand][$randName], $russianSurnameStartLetters[$randSurname]);
    }

    private function getDate(): string
    {
        // Задайте начальную и конечную даты и времена
        $startDate = Carbon::create(2022, 1, 1, 0, 0, 0); // 2022-01-01 00:00:00
        $endDate = Carbon::create(2023, 8, 25, 23, 59, 59); // 2023-08-25 23:59:59

        // Получите случайную дату и время в этом диапазоне
        $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
        $randomDate = Carbon::createFromTimestamp($randomTimestamp);

        // Выведите случайную дату и время
        return $randomDate->toDateTimeString();
    }

    private function getFakeReview(): string
    {
        $positiveReviews = [
            "Доволен покупкой и результатами! Спортивное питание помогло мне достичь своих целей.",
            "Это была отличная покупка. Результаты просто потрясающие!",
            "Я приобрел этот товар несколько недель назад и уже вижу улучшения.",
            "Не могу поверить, насколько эффективными оказались эти товары. Рекомендую!",
            "Спортивное питание этой марки - это настоящий находка! Я доволен каждой покупкой.",
            "Мои результаты на тренировках заметно улучшились после того, как я начал использовать эти продукты.",
            "Спортивное питание сделало мою жизнь активнее и здоровее. Очень доволен результатом!",
            "Я был скептически настроен в начале, но этот товар действительно оправдал мои ожидания.",
            "Это был отличный выбор. Я чувствую себя более энергичным и здоровым благодаря этим продуктам.",
            "Супер результат!",
            "Лучшая покупка!",
            "Эффективно!",
            "Рекомендую!",
            "Просто восхитительно!",
            "Удивительно!",
            "Замечательно!",
            "Отлично сработало!",
            "Мои тренировки стали легче!",
            "Полностью доволен!",
            "Результат виден!",
            "Невероятно!",
            "Супер!",
            "Эффективное питание!",
            "Лучший выбор!",
            "Заметные улучшения!",
            "Доволен каждой капсулой!",
            "Очень эффективно!",
            "Спасибо за качество!",
            "Продукты работают!"
        ];

        return $positiveReviews[
                mt_rand(0, count($positiveReviews) - 1)
            ];
    }
}
