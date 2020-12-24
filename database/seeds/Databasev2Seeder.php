<?php

use Illuminate\Database\Seeder;

class Databasev2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            FillBaseProductsAndSkusTable::class,
            FillCategoryManufacturerSubcategory::class,
            AttributeValuesTable::class,
            TagsSeeder::class,
            TaggableSeeder::class,
            ImageSeeder::class,
            ThumbSeeder::class,
        ]);
    }
}
