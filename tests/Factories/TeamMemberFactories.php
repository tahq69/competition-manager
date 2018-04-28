<?php namespace Tests\Factories;

use App\Contracts\MemberRole;
use App\TeamMember;
use App\User;
use Tests\RoleHelper;

/**
 * Trait TeamMemberFactories
 * @package Tests
 */
trait TeamMemberFactories
{
    protected function createTeamManager(int $teamId, int $userId = 0): TeamMember
    {
        $user = $this->getUser($userId);
        $member = factory(TeamMember::class)->create([
            'user_id' => $user->id,
            'team_id' => $teamId
        ]);

        RoleHelper::memberSync(
            $member,
            MemberRole::MANAGE_TEAMS,
            MemberRole::MANAGE_MEMBERS,
            MemberRole::MANAGE_MEMBER_ROLES
        );

        return $member;
    }

    private function getUser(int $userId): User
    {
        if ($userId == 0) {
            return factory(User::class)->create();
        }

        return User::find($userId, ['id']);
    }
}
