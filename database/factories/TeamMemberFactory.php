<?php

use App\TeamMember;
use Faker\Generator as Faker;

$factory->define(TeamMember::class, function (Faker $faker) {
    return [
        'membership_type' => TeamMember::MEMBER,
        'name' => $faker->name,
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'team_id' => function () {
            return factory(App\Team::class)->create()->id;
        },
    ];
});
