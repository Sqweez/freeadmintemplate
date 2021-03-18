<?php

use Illuminate\Database\Seeder;

class FillCategoryManufacturerSubcategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manufacturers = DB::table('manufacturer_products')->get();
        $categories = DB::table('category_product')->get();
        $subcategories = DB::table('subcategory_product')->get();

        $manufacturers->each(function ($manufacturer) {
            $product_sku_id = $manufacturer->product_id;
            $product_sku = DB::table('product_sku')->where('id', $product_sku_id)->first();

            if ($product_sku) {
                $product_id = $product_sku->product_id;
                DB::table('products')->where('id', $product_id)->update([
                    'manufacturer_id' => $manufacturer->manufacturer_id
                ]);
            }
        });

        $categories->each(function ($category) {
            $product_sku_id = $category->product_id;
            $product_sku = DB::table('product_sku')->where('id', $product_sku_id)->first();

            if ($product_sku) {
                $product_id = $product_sku->product_id;
                DB::table('products')->where('id', $product_id)->update([
                    'category_id' => $category->category_id
                ]);
            }
        });

        $subcategories->each(function ($subcategory) {
            $product_sku_id = $subcategory->product_id;
            $product_sku = DB::table('product_sku')->where('id', $product_sku_id)->first();

            if ($product_sku) {
                $product_id = $product_sku->product_id;
                DB::table('products')->where('id', $product_id)->update([
                    'subcategory_id' => $subcategory->subcategory_id
                ]);
            }
        });
    }
}
