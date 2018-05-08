<?php namespace App\Http\Requests;

use App\Contracts\ITeamMemberRepository as IMembers;
use App\Http\Requests\CompetitionManagePolicy as ICompetition;
use App\TeamMember;
use Auth;

/**
 * Class MemberRolesPolicy
 *
 * @package App\Http\Requests
 */
class MemberRolesPolicy
{
    /**
     * @var \App\Contracts\ITeamMemberRepository
     */
    private $members;

    /**
     * @var \App\Http\Requests\CompetitionManagePolicy
     */
    private $competition;

    /**
     * MemberRolesPolicy constructor.
     *
     * @param \App\Contracts\ITeamMemberRepository       $members
     * @param \App\Http\Requests\CompetitionManagePolicy $competition
     */
    public function __construct(IMembers $members, ICompetition $competition)
    {
        $this->members = $members;
        $this->competition = $competition;
    }

    /**
     * Determines is the provided member identifier part of the team.
     *
     * @param int $teamId   Team identifier.
     * @param int $memberId Member identifier.
     *
     * @return bool Is the member part of the team.
     */
    public function isMember(int $teamId, int $memberId)
    {
        /** @var TeamMember $member */
        $member = $this->members->find($memberId, ['id', 'team_id']);

        return $member->team_id == $teamId;
    }

    /**
     * @param  int $teamId
     *
     * @return bool
     */
    public function isManager(int $teamId)
    {
        $isManager = $this->members
            ->filterByTeam($teamId)
            ->filterByUser(Auth::id())
            ->filterByMembership(TeamMember::MANAGER)
            ->count(true);

        return $isManager > 0;
    }

    /**
     * Has the authenticated user role in the provided team.
     *
     * @param int    $teamId Team identifier.
     * @param string $role   Required role.
     *
     * @return bool
     */
    public function hasRole(int $teamId, string $role)
    {
        $authUserMembers = $this->members
            ->filterByTeam($teamId)
            ->filterByUser(Auth::id())
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
