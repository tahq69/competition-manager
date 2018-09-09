<?php

use Faker\Generator as Faker;

$factory->define(App\Message::class, function (Faker $faker) {
    return [
        'body' => $faker->sentences(3, true),
        'from_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'from_name' => function ($msg) {
            return App\User::find($msg['from_id'])->name;
        },
        'importance_level' => $faker->numberBetween(1, 10),
        'is_read' => $faker->boolean,
        'reply' => 0,
        'reply_count' => 0,
        'subject' => $faker->sentence,
        'to_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'to_name' => function ($msg) {
            return App\User::find($msg['to_id'])->name;
        },
        'type' => $faker->randomElement(App\Message::TYPES),
        'payload' => '',
    ];
});
