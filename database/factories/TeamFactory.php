<?php

use Faker\Generator as Faker;

$factory->define(App\Team::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'short' => $faker->companySuffix,
        'created_by' => 4,
        'created_by_name' => 'team.manager',
    ];
});
