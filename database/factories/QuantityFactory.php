<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\ProductQuantity::class, function (Faker $faker) {
    return [
        'product_id' => mt_rand(1, 110),
        'quantity' => mt_rand(1, 1000),
        'store_id' => mt_rand(1, 3)
    ];
});
