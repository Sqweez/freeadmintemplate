<?php

namespace App\Console\Commands\Utils;

use App\Product;
use App\v2\Models\KaspiEntityProduct;
use App\v2\Models\ProductComment;
use App\v2\Models\ProductSku;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreateKaspiNowProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kaspi:now';

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
        $id = 71;
        $products = Product::query()
            ->whereManufacturerId($id)
            ->get();
        foreach ($products as $product) {
            $kaspi = KaspiEntityProduct::query()
                ->whereKaspiEntityId(2)
                ->whereProductId($product->id)
                ->first();
            if ($kaspi) {
                $this->line('Updating product: ' . $product->id);
                $kaspi->is_visible = true;
                $kaspi->save();
            } else {
                $this->line('Creating product: ' . $product->id);
                $kaspi = new KaspiEntityProduct();
                $kaspi->kaspi_entity_id = 2;
                $kaspi->product_id = $product;
                $kaspi->is_visible = true;
                $kaspi->price = $product->price;
                $kaspi->save();
            }
        }
    }
}
