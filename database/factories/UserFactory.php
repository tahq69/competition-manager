<?php

use Faker\Generator as Faker;


$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('password'),
        'remember_token' => str_random(10),
    ];
});

$factory->state(App\User::class, 'super_admin', [
    'name' => 'TAHQ69',
    'email' => 'tahq69@gmail.com',
]);

$factory->state(App\User::class, 'post_creator', [
    'name' => 'post.creator',
    'email' => 'post.creator@crip.lv',
]);

$factory->state(App\User::class, 'post_manager', [
    'name' => 'post.manager',
    'email' => 'post.manager@crip.lv',
]);

$factory->state(App\User::class, 'team_owner', [
    'name' => 'team.owner',
    'email' => 'team.owner@crip.lv',
]);
