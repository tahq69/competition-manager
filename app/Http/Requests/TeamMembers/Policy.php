<?php namespace App\Http\Requests\TeamMembers;

use App\Http\Requests\MemberRolesPolicy;
use App\Http\Requests\UserRolesPolicy;
use App\Role;

/**
 * Class Policy
 * @package App\Http\Requests\TeamMembers
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
     * @param UserRolesPolicy $user
     * @param MemberRolesPolicy $member
     */
    public function __construct(UserRolesPolicy $user, MemberRolesPolicy $member)
    {
        $this->user = $user;
        $this->member = $member;
    }

    /**
     * @param int $teamId
     * @return bool
     */
    public function canStore(int $teamId): bool
    {
        if (!$this->user->authorized()) return false;
        $user = $this->user->id;

        // Super admin or team creator can edit any team details/members/roles.
        $globalRoles = [Role::SUPER_ADMIN, Role::CREATE_TEAMS];
        if ($this->user->hasAnyRole($globalRoles)) return true;

        // If authenticated user is manager of the member team, allow any action.
        if ($this->member->isManager($teamId, $user)) return true;

        // Only simple members requires roles to access team member data.
        return $this->member->hasRole($teamId, $user, Role::MANAGE_MEMBERS);
    }

    /**
     * @param int $teamId
     * @return bool
     */
    public function canUpdate(int $teamId): bool
    {
        return $this->canStore($teamId);
    }
}
