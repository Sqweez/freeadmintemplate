<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\ProductBatch::class, function (Faker $faker) {
    return [
        'product_id' => mt_rand(1, 107),
        'quantity' => mt_rand(1, 100),
        'store_id' => mt_rand(1, 3),
        'purchase_price' => mt_rand(500, 8000)
    ];
});
