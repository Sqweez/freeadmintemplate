<?php

use Illuminate\Database\Seeder;

class Ironv2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            FillProductsAndSkuTable::class,
            CategorySubcategoryManufacturer::class,
            AttributeValueTable::class,
            TagSeeder::class,
            TagablesSeeder::class,
            ImageSeeder::class,
            ThumbSeeder::class,
        ]);
    }
}
