<?php

use Illuminate\Database\Seeder;

class FillProductBelongsColumns extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoryProduct = \App\CategoryProduct::select(['category_id', 'product_id'])->groupBy('product_id')->get();
        $subcategoryProduct = \App\SubcategoryProduct::select(['subcategory_id', 'product_id'])->groupBy('product_id')->get();
        $manufacturerProduct = \App\ManufacturerProducts::select(['manufacturer_id', 'product_id'])->groupBy('product_id')->get();


        $categoryProduct->each(function ($category) {
            $product = \App\v2\Models\Product::find($category->product_id);
            if ($product) {
             $product->update(['category_id' => $category->category_id]);
            }
        });

        $subcategoryProduct->each(function ($category) {
            $product = \App\v2\Models\Product::find($category->product_id);
            if ($product) {
             $product->update(['subcategory_id' => $category->subcategory_id]);
            }
        });

        $manufacturerProduct->each(function ($category) {
            $product = \App\v2\Models\Product::find($category->product_id);
            if ($product) {
             $product->update(['manufacturer_id' => $category->manufacturer_id]);
            }
        });


    }
}
