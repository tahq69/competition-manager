<?php

use App\Area;
use App\Competition;
use Faker\Generator as Faker;

$nr = 1;

$factory->define(Area::class, function (Faker $faker) use (&$nr) {
    return [
        'competition_id' => function () {
            return factory(Competition::class)->create()->id;
        },
        'description' => join('<br><br>', $faker->paragraphs(3)),
        'nr' => $nr++,
        'title' => $faker->company,
        'type' => $faker->randomElement(Area::TYPES),
    ];
});
