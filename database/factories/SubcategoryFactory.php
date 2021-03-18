<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Subcategory::class, function (Faker $faker) {
    return [
        'subcategory_name' => $faker->word,
        'category_id' => rand(1, 20)
    ];
});
