<?php namespace App\Http\Requests\TeamMemberRoles;

use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Http\Requests\MemberRolesPolicy;
use App\Http\Requests\UserRolesPolicy;
use App\Role;

/**
 * Class Policy
 *
 * @package App\Http\Requests\TeamMemberRoles
 */
class Policy
{
    /**
     * @var \App\Http\Requests\UserRolesPolicy
     */
    private $user;

    /**
     * @var \App\Http\Requests\MemberRolesPolicy
     */
    private $member;

    /**
     * Policy constructor.
     *
     * @param \App\Http\Requests\UserRolesPolicy   $user
     * @param \App\Http\Requests\MemberRolesPolicy $member
     */
    public function __construct(UserRolesPolicy $user, MemberRolesPolicy $member)
    {
        $this->user = $user;
        $this->member = $member;
    }

    /**
     * Determine is the authenticated user has access to list team member roles.
     *
     * @param int $teamId
     * @param int $memberId
     *
     * @return bool
     */
    public function canList(int $teamId, int $memberId): bool
    {
        if (!$this->user->authorized()) return false;

        // Super admin or team creator can edit any team details/members/roles.
        if ($this->user->hasRole(UserRole::CREATE_TEAMS)) return true;

        // If member is not from provided team, deny any action on it.
        if (!$this->member->isMember($teamId, $memberId)) return false;

        // If authenticated user is manager of the member team, allow any action.
        if ($this->member->isManager($teamId)) return true;

        // Only simple members requires roles to access team member data.
        return $this->member->hasRole($teamId, MemberRole::MANAGE_MEMBER_ROLES);
    }

    /**
     * Determine is the authenticated user has access to update team member
     * roles.
     *
     * @param int $teamId
     * @param int $memberId
     *
     * @return bool
     */
    public function canUpdate(int $teamId, int $memberId): bool
    {
        return $this->canList($teamId, $memberId);
    }
}
