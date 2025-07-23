<?php

namespace App\Console\Commands\Utils;

use App\Product;
use App\v2\Models\KaspiEntityProduct;
use App\v2\Models\ProductSku;
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
        #$categoriesId = [7, 11, 9, 15, 18, 16];
        $skuIds = [
            '9760',
            '9778',
            '9831',
            '11665',
            '11670',
            '8655',
            '9864',
            '11581',
            '11023',
            '11778',
            '11660',
            '9400',
            '10539',
            '9873',
            '11728',
            '11606',
            '11687',
            '181',
            '9401',
            '8419',
            '11703',
            '11640',
            '9549',
            '11395',
            '11610',
            '9403',
            '8305',
            '1361',
            '1531',
            '11620',
            '11017',
            '9578',
            '9560',
            '2004',
            '2709',
            '11394',
            '9622',
            '7421',
            '11510',
            '6593',
            '8163',
            '9553',
            '11397',
            '9758',
            '11777',
            '11700',
            '11502',
            '11682',
            '11402',
            '9763',
            '9762',
            '11677',
            '11668',
            '11624',
            '11625',
            '11489',
            '11396',
            '11426',
            '11367',
            '11779',
            '11690',
            '9580',
            '11471',
            '11729',
            '11619',
            '9868',
            '11339',
            '10879',
            '9781',
            '11016',
            '11607',
            '8791',
            '11467',
            '11702',
            '11675',
            '11627',
            '11667',
            '11663',
            '9872',
            '11622',
            '11623',
            '11492',
            '11723',
            '205',
            '11506',
            '7997',
            '11358',
            '9787',
            '11364',
            '11338',
            '7411',
            '7100',
            '11669',
            '11701',
            '11580',
            '11576',
            '11170',
            '11671',
            '11018',
            '4840',
            '6637',
            '9398',
            '11272',
            '11509',
            '11466',
            '11470',
            '9396',
            '9399',
            '11341',
            '8418',
            '11727',
            '5168',
            '11673',
            '11608',
            '11123',
            '11582',
            '10877',
            '5031',
            '359',
            '896',
            '914',
            '9826',
            '5167',
            '9405',
            '5842',
            '11025',
            '11022',
            '11418',
            '9406',
            '11280',
            '11135',
            '9566',
            '11443',
            '8526',
            '9021',
            '11134',
            '9860',
            '9859',
            '4512',
            '8527',
            '7878',
            '11279',
            '9561',
            '5125',
            '1744'
        ];
        $kaspiEntityId = 2;

        /** @var Collection|Product[] $products */
        $products = ProductSku::query()
            ->whereIn('id', $skuIds)
            ->get()
            ->pluck('product_id');

        $products = Product::whereIn('id', $products)
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
