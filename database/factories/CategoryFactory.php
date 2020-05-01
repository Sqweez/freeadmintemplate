<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use Faker\Generator as Faker;

$factory->define(\App\SubcategoryProduct::class, function (Faker $faker) {
    return [
        'product_id' => rand(1, 1000),
        'subcategory_id' => rand(10, 58),
    ];
});
