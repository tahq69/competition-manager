<?php

use Faker\Generator as Faker;

$factory->define(\App\Discipline::class, function (Faker $faker) {

    return [
        'title' => $faker->company,
        'short' => $faker->companySuffix,
        'type' => \App\Discipline::KICKBOXING,
        'game_type' => join('<br><br>', $faker->paragraphs(3)),
        'description' => join('<br><br>', $faker->paragraphs(10)),

        'competition_id' => function () {
            return factory(\App\Competition::class)->create()->id;
        },
    ];
});
