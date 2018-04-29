<?php namespace Tests\Factories;

use App\Contracts\MemberRole;
use App\Team;
use App\TeamMember;
use Tests\RoleHelper;

/**
 * Class TeamFactories
 *
 * @package Tests
 */
trait TeamFactories
{
    protected function createTeam(
        array $managers = [],
        array $roles = [MemberRole::MANAGE_COMPETITIONS],
        int $credits = 0
    ): Team
    {
        $attributes = ['_credits' => $credits];
        $team = factory(Team::class)->times(3)->create($attributes)[1];

        foreach ($managers as $user) {
            $manager = factory(TeamMember::class)->create([
                'team_id' => $team->id,
                'membership_type' => TeamMember::MANAGER,
                'user_id' => $user->id,
            ]);

            forward_static_call_array(
                [RoleHelper::class, 'memberSync'],
                array_merge([$manager], $roles)
            );
        }

        return $team;
    }
}
