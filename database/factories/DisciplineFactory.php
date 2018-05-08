<?php

use App\Competition;
use App\Discipline;
use Faker\Generator as Faker;

$factory->define(Discipline::class, function (Faker $faker) {

    return [
        'title' => $faker->company,
        'short' => $faker->companySuffix,
        'type' => Discipline::TYPE_KICKBOXING,
        'game_type' => join('<br><br>', $faker->paragraphs(3)),
        'description' => join('<br><br>', $faker->paragraphs(10)),
        'category_group_type' => Discipline::CAT_TYPE_AGE,
        'category_type' => Discipline::CAT_TYPE_WEIGHT,

        'competition_id' => function () {
            return factory(Competition::class)->create()->id;
        },
        'team_id' => function () {
            return factory(\App\Team::class)->create()->id;
        },
    ];
});
