<?php namespace App\Http\Requests;

use App\Contracts\ITeamMemberRepository as IMembers;
use App\TeamMember;

/**
 * Class MemberRolesPolicy
 * @package App\Http\Requests
 */
class MemberRolesPolicy
{
    /**
     * @var \App\Contracts\ITeamMemberRepository
     */
    private $members;

    /**
     * MemberRolesPolicy constructor.
     * @param IMembers $members
     */
    public function __construct(IMembers $members)
    {
        $this->members = $members;
    }

    /**
     * @param  int $teamId
     * @param  int $memberId
     * @return bool
     */
    public function isMember(int $teamId, int $memberId)
    {
        /** @var TeamMember $member */
        $member = $this->members->find($memberId, ['id', 'team_id']);

        return $member->team_id == $teamId;
    }

    /**
     * @param  int $teamId
     * @param  int $userId
     * @return bool
     */
    public function isManager(int $teamId, int $userId)
    {
        $isManager = $this->members
            ->filterByTeam($teamId)
            ->filterByUser($userId)
            ->filterByMembership(TeamMember::MANAGER)
            ->count(true);

        return $isManager > 0;
    }

    /**
     * @param  int $teamId
     * @param  int $userId
     * @param  string $role
     * @return bool
     */
    public function hasRole(int $teamId, int $userId, string $role)
    {
        $authUserMembers = $this->members
            ->filterByTeam($teamId)
            ->filterByUser($userId)
            ->withTeamMemberRoles()
            ->get();

        $hasRole = false;

        // Determine whenever at least one membership with required role exists.
        $authUserMembers->each(function ($member) use (&$hasRole, $role) {
            if (collect($member->roles)->contains('key', $role))
                $hasRole = true;
        });

        return $hasRole;
    }
}
