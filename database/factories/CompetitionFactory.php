<?php

use App\Competition;
use App\Team;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Competition::class, function (Faker $faker) {
    return [
        'ambulance' => join('<br><br>', $faker->paragraphs(3)),
        'cooperation' => join('<br><br>', $faker->paragraphs(10)),
        'equipment' => join('<br><br>', $faker->paragraphs(5)),
        'invitation' => join('<br><br>', $faker->paragraphs(2)),
        'organization_date' => (new Carbon('now'))->addDays($faker->numberBetween(20, 40)),
        'price' => join('<br><br>', $faker->paragraphs(5)),
        'prizes' => join('<br><br>', $faker->paragraphs(10)),
        'program' => join('<br><br>', $faker->paragraphs(20)),
        'registration_till' => function ($competition) {
            return $competition['organization_date']->addDays(-1);
        },
        'rules' => join('<br><br>', $faker->paragraphs(40)),
        'subtitle' => $faker->sentence(),
        'title' => $faker->sentence(),

        'team_id' => function () {
            return factory(Team::class)->create()->id;
        },
        'team_name' => function ($competition) {
            return Team::find($competition['team_id'])->name;
        },
        'team_short' => function ($competition) {
            return Team::find($competition['team_id'])->short;
        },

        'judge_name' => $faker->name,
        'judge_id' => 5
    ];
});
