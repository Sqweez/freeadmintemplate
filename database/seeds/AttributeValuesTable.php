<?php

use App\AttributeProduct;
use App\v2\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeValuesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attribute_products = AttributeProduct::all()->groupBy('attribute_value');
        DB::table('attribute_values')->truncate();
        DB::table('attributable')->truncate();
        $attribute_products->each(function ($item, $key) {
            $attribute_value = $key;
            $attribute_id = $item[0]['attribute_id'];
            $attribute = AttributeValue::create([
                'attribute_value' => $attribute_value,
                'attribute_id' => $attribute_id
            ]);
            $id = $attribute->id;
            collect($item)->pluck('product_id')->each(function ($product) use ($id, $attribute_id) {
                $sku = DB::table('product_sku')->find($product);
                if ($sku) {
                    if ($attribute_id == 1 || $attribute_id == 3) {
                        DB::table('attributable')->insert([
                            'attribute_value_id' => $id,
                            'attributable_id' => $sku->id,
                            'attributable_type' => 'App\v2\Models\ProductSku'
                        ]);
                        DB::table('products')->where('id', $sku->product_id)->update(
                            [
                                'grouping_attribute_id' => $attribute_id
                            ]
                        );
                    } else {
                        $product_id = $sku->product_id;
                        $alreadyHas = DB::table('attributable')
                            ->where('attributable_id', $product_id)
                            ->where('attributable_type', 'App\v2\Models\Product')
                            ->where('attribute_value_id', $id)
                            ->first();

                        if (!$alreadyHas) {
                            DB::table('attributable')->insert([
                                'attribute_value_id' => $id,
                                'attributable_id' => $sku->product_id,
                                'attributable_type' => 'App\v2\Models\Product'
                            ]);
                        }
                    }
                }
            });
        });
    }
}
