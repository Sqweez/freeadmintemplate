<?php

use Illuminate\Database\Seeder;
use App\ProductImage;
use App\v2\Models\Image;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('images')->truncate();
        DB::table('imagable')->truncate();

        ProductImage::all()->groupBy('product_image')->each(function ($item, $key) {
            $image_id = Image::create([
                'image' => $key
            ])->id;

            $imagables = collect($item)->pluck('product_id')->map(function ($product_id) use ($image_id) {
                $sku = DB::table('product_sku')->find($product_id);
                if ($sku) {
                    return [
                        'image_id' => $image_id,
                        'imagable_id' => $sku->product_id,
                        'imagable_type' => 'App\v2\Models\Product'
                    ];
                } else {
                    return null;
                }
            })->filter(function ($item) {return $item !== null; })->unique(function ($i) {
                return $i['imagable_id'].$i['image_id'];
            })->values()->all();


            DB::table('imagable')->insert($imagables);
        });
    }
}
