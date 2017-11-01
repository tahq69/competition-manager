<?php

use Illuminate\Database\Seeder;

/**
 * Class TeamMembersTableSeeder
 */
class TeamMembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $team = \App\Team::query()->firstOrFail();
        factory(\App\TeamMember::class, 20)->create(['team_id' => $team->id]);
    }
}
