<?php

use App\CategoryGroup;
use App\Discipline;
use Faker\Generator as Faker;

$factory->define(CategoryGroup::class, function (Faker $faker) {
    return [
        'competition_id' => function () {
            return factory(\App\Competition::class)->create()->id;
        },
        'team_id' => function () {
            return factory(\App\Team::class)->create()->id;
        },
        'discipline_id' => function () {
            return factory(\App\Discipline::class)->create()->id;
        },
        'discipline_short' => function ($group) {
            return Discipline::find($group['discipline_id'])->short;
        },
        'discipline_title' => function ($group) {
            return Discipline::find($group['discipline_id'])->title;
        },
        'order' => function ($group) {
            return CategoryGroup::where('discipline_id', $group['discipline_id'])->count() + 1;
        },
        'type' => Discipline::CAT_TYPE_AGE,
        'title' => $faker->company,
        'short' => $faker->companySuffix,
        'rounds' => $faker->numberBetween(1, 5),
        'time' => $faker->numberBetween(60, 180),
        'min' => $faker->numberBetween(10, 40),
        'max' => function ($group) {
            return $group['min'] + 2;
        },
    ];
});
