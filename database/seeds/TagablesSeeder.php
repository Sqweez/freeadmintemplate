<?php

use Illuminate\Database\Seeder;

class TagablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('taggables')->truncate();
        $taggables = ProductTag::select(['product_id', 'tag_id'])->get()->map(function ($tag) {
            $sku = DB::table('product_sku')->where('id', $tag['product_id'])->first();
            if ($sku) {
                $taggable_id = $sku->product_id;
                return [
                    'taggable_id' => $taggable_id,
                    'taggable_type' => 'App\v2\Models\Product',
                    'tag_id' => $tag['tag_id']
                ];
            }

            return [];
        })->filter(function ($item) {
            return count($item) > 0;
        })->unique(function ($item) {
            return $item['taggable_id'].$item['tag_id'];
        })->values()->all();

        DB::table('taggables')->insert($taggables);

    }
}
