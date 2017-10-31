<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(),
        'body' => join('<br><br>', $faker->paragraphs(10)),
        'image' => $faker->imageUrl(1110, 300),
        'state' => $faker->randomElement(['PUBLISHED', 'DRAFT', 'PENDING', 'TRASH', 'PRIVATE']),
        'publish_at' => $faker->dateTimeThisMonth,
        'locale' => $faker->randomElement(['lv', 'en']),
    ];
});
