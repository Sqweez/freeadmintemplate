<?php

namespace App\Console\Commands\Utils;

use App\Service\YandexTranslator;
use App\v2\Models\Product;
use Illuminate\Console\Command;

class TranslateProductDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'description:translate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Осуществляет перевод описания товаров на казахский язык';

    private $translationService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->translationService = new YandexTranslator();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $products = $this->getProductsWithoutTranslation();
        foreach ($products as $key => $product) {
            try {
                $this->line('Осталось: ' . ($products->count() - ($key)));
                $this->line('Текущий товар: ' . $product->product_name);
                $result = $this->translationService->translate($product->product_description);
                $encoded = json_decode($result);
                $text = $encoded->translations[0]->text;
                $product->update([
                    'product_description_kaz' => $text
                ]);
            }
            catch (\Exception $exception) {
                $this->line($exception->getMessage());
                continue;
            }
        }
    }

    private function getProductsWithoutTranslation()
    {
        return Product::query()
            ->whereNull('product_description_kaz')
            ->whereNotNull('product_description')
            ->select(['id', 'product_name', 'product_description_kaz', 'product_description'])
            ->get();
    }
}
