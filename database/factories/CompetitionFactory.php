<?php

use Faker\Generator as Faker;

$factory->define(\App\Competition::class, function (Faker $faker) {
    $date = (new \Carbon\Carbon('now'))->addDays($faker->numberBetween(20, 40));
    $team = factory(\App\Team::class)->create();
    return [
        'ambulance' => join('<br><br>', $faker->paragraphs(3)),
        'cooperation' => join('<br><br>', $faker->paragraphs(10)),
        'equipment' => join('<br><br>', $faker->paragraphs(5)),
        'invitation' => join('<br><br>', $faker->paragraphs(2)),
        'organization_date' => $date,
        'price' => join('<br><br>', $faker->paragraphs(5)),
        'prizes' => join('<br><br>', $faker->paragraphs(10)),
        'program' => join('<br><br>', $faker->paragraphs(20)),
        'registration_till' => $date->addDays(-1),
        'rules' => join('<br><br>', $faker->paragraphs(40)),
        'subtitle' => $faker->sentence(),
        'title' => $faker->sentence(),

        'team_id' => $team->id,
        'team_name' => $team->name,
        'team_short' => $team->short,

        'judge_name' => $faker->name,
        'judge_id' => 5
    ];
});
