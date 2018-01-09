<?php

use App\Competition;
use App\Discipline;
use Illuminate\Database\Seeder;

/**
 * Class CompetitionsTableSeeder
 */
class CompetitionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        factory(Competition::class, 10)->create()->each(function (Competition $cm) {
            $cm->disciplines()->saveMany(
                factory(Discipline::class, 2)->create(
                    ['competition_id' => $cm->id]
                )
            );
        });
    }
}
