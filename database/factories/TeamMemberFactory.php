<?php

use Faker\Generator as Faker;

$factory->define(\App\TeamMember::class, function (Faker $faker) {
    return [
        'membership_type' => \App\TeamMember::MEMBER,
        'name' => $faker->name,
    ];
});
