<?php namespace App\Http\Requests\TeamMemberRoles;

use App\Contracts\ITeamMemberRepository as IMembers;
use App\Http\Requests\UserRolesPolicy;
use App\Role;

/**
 * Class Policy
 * @package App\Http\Requests\TeamMemberRoles
 */
class Policy
{
    /**
     * @var \App\Http\Requests\UserRolesPolicy
     */
    private $user;

    /**
     * @var \App\Contracts\ITeamMemberRepository
     */
    private $members;

    /**
     * Policy constructor.
     * @param \App\Http\Requests\UserRolesPolicy $user
     * @param \App\Contracts\ITeamMemberRepository $members
     */
    public function __construct(UserRolesPolicy $user, IMembers $members)
    {
        $this->user = $user;
        $this->members = $members;
    }

    /**
     * Determine is the authenticated user has access to list team member roles.
     * @param  int $teamId
     * @param  int $memberId
     * @return bool
     */
    public function canList(int $teamId, int $memberId): bool
    {
        if (!$this->user->authorized()) return false;

        if ($this->user->hasRole(Role::SUPER_ADMIN)) return true;

        /** @var \App\TeamMember $member */
        $member = $this->members->find($memberId, ['id', 'team_id']);
        // If member is not from provided team, deny any other action.
        if ($member->team_id != $teamId) return false;

        $authUserMembers = $this->members
            ->filterByTeam($teamId)
            ->filterByUser($this->user->id)
            ->withTeamMemberRoles()
            ->get();

        $canList = false;

        $authUserMembers->each(function ($member) use (&$canList) {
            $roles = collect($member->roles);

            if ($roles->contains('key', Role::MANAGE_MEMBER_ROLES))
                $canList = true;
        });

        return $canList;
    }
}
