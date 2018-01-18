<?php

use App\Competition;
use App\Discipline;
use Faker\Generator as Faker;

$factory->define(Discipline::class, function (Faker $faker) {

    return [
        'title' => $faker->company,
        'short' => $faker->companySuffix,
        'type' => Discipline::KICKBOXING,
        'game_type' => join('<br><br>', $faker->paragraphs(3)),
        'description' => join('<br><br>', $faker->paragraphs(10)),

        'competition_id' => function () {
            return factory(Competition::class)->create()->id;
        },
    ];
});
