<?php namespace Tests;

use App\Contracts\MemberRole;
use App\TeamMember;

/**
 * Class TeamFactories
 * @package Tests
 */
trait TeamFactories
{
    protected function createTeam(array $managers = [])
    {
        $team = factory(\App\Team::class)->times(3)->create()[1];

        foreach ($managers as $user) {
            $manager = factory(TeamMember::class)->create([
                'team_id' => $team->id,
                'membership_type' => TeamMember::MANAGER,
                'user_id' => $user->id,
            ]);

            RoleHelper::memberSync($manager, MemberRole::MANAGE_COMPETITIONS);
        }

        return $team;
    }
}
