<?php

namespace App\Console\Commands\Utils;

use App\Product;
use App\v2\Models\KaspiEntityProduct;
use Illuminate\Console\Command;

class ReformatKaspiRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kaspi:reformat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Реформатирует каспи-отношения к новому формату';

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
     */
    public function handle()
    {
        Product::query()
            ->where('kaspi_product_price', '>', 0)
            ->where('is_kaspi_visible', true)
            ->each(function (Product $product) {
                KaspiEntityProduct::create([
                    'kaspi_entity_id' => __hardcoded(1),
                    'product_id' => $product->id,
                    'price' => $product->kaspi_product_price,
                    'is_visible' => $product->is_kaspi_visible,
                ]);
            });
    }
}
