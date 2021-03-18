<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\ProductTag::class,  function (Faker $faker) {
    return [
        'product_id' => mt_rand(1, 107),
        'tag_id' => mt_rand(1, 1000)
    ];
});
