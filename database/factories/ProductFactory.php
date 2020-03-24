<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'product_name' => $faker->sentence(3),
        'product_description' => $faker->sentence(20),
        'product_price' => rand(100, 30000),
        'product_barcode' => rand(1000000, 999999),
    ];
});
