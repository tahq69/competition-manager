<?php

use App\Area;
use App\Category;
use App\CategoryGroup;
use App\Competition;
use App\Discipline;
use Faker\Generator as Faker;

$nr = 1;

$factory->define(Category::class, function (Faker $faker) use (&$nr) {
    return [
        'area_id' => function () {
            return factory(Area::class)->create()->id;
        },
        'category_group_id' => function () {
            return factory(CategoryGroup::class)->create()->id;
        },
        'category_group_short' => function ($category) {
            return CategoryGroup::find($category['category_group_id'])->short;
        },
        'category_group_title' => function ($category) {
            return CategoryGroup::find($category['category_group_id'])->title;
        },
        'competition_id' => function () {
            return factory(Competition::class)->create()->id;
        },
        'discipline_id' => function () {
            return factory(Discipline::class)->create()->id;
        },
        'discipline_short' => function ($category) {
            return Discipline::find($category['discipline_id'])->short;
        },
        'discipline_title' => function ($category) {
            return Discipline::find($category['discipline_id'])->title;
        },
        'order' => $nr++,
        'short' => $faker->companySuffix,
        'title' => $faker->company,
    ];
});
