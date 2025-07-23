<?php

namespace App\Console\Commands\Utils;

use App\Product;
use App\v2\Models\KaspiEntityProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateKaspiNowProducts extends Command
{
    protected $signature = 'kaspi:marina';
    protected $description = 'Create or update KaspiEntityProduct records for a given manufacturer';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $manufacturerId = 608;
        $categoriesId = [7, 11, 9, 15, 18, 16];
        $kaspiEntityId = 2;

        /** @var Collection|Product[] $products */
        $products = Product::query()
            #->where('manufacturer_id', $manufacturerId)
            ->WhereIn('category_id', $categoriesId)
            ->get();

        if ($products->isEmpty()) {
            $this->warn("No products found");
            return;
        }

        $this->info("Found {$products->count()} products. Processing...");

        DB::transaction(function () use ($products, $kaspiEntityId) {
            foreach ($products as $product) {
                $this->line("Processing product ID {$product->product_name}");
                /** @var KaspiEntityProduct|null $kaspi */
                $kaspi = KaspiEntityProduct::query()
                    ->where('kaspi_entity_id', $kaspiEntityId)
                    ->where('product_id', $product->id)
                    ->first();

                if ($kaspi) {
                    $this->line("Updating product {$product->id}");
                    $kaspi->is_visible = true;
                } else {
                    $this->line("Creating product ID {$product->id}");
                    $kaspi = new KaspiEntityProduct([
                        'kaspi_entity_id' => $kaspiEntityId,
                        'product_id' => $product->id,
                        'is_visible' => true,
                        'price' => $product->price,
                    ]);
                }

                $kaspi->save();
            }
        });

        $this->info('Done.');
    }
}
