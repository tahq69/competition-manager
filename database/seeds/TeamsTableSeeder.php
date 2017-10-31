<?php

use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managerEmail = UsersTableSeeder::TEAM_MANAGER_EMAIL;
        $manager = \App\User::where('email', '=', $managerEmail)
            ->firstOrFail();

        $team = new \App\Team([
            'name' => 'Team Manager Team 1',
            'short' => 'TMT1',
            'created_by' => $manager->id,
            'created_by_name' => $manager->name,
        ]);

        $team->save();

        $temMember = new \App\TeamMember([
            'membership_type' => \App\TeamMember::MANAGER,
            'user_id' => $manager->id,
            'name' => $manager->name,
            'team_id' => $team->id,
        ]);

        $temMember->save();
    }
}
