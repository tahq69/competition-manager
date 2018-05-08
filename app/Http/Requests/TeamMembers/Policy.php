<?php namespace App\Http\Requests\TeamMembers;

use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Http\Requests\MemberRolesPolicy;
use App\Http\Requests\UserRolesPolicy;

/**
 * Class Policy
 *
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
     * @param int $teamId
     *
     * @return bool
     */
    public function canStore(int $teamId): bool
    {
        if (!$this->user->authorized()) return false;

        // Super admin or team creator can edit any team details/members/roles.
        if ($this->user->hasRole(UserRole::CREATE_TEAMS)) return true;

        // If authenticated user is manager of the member team, allow any action.
        if ($this->member->isManager($teamId)) return true;

        // Only simple members requires roles to access team member data.
        return $this->member->hasRole($teamId, MemberRole::MANAGE_MEMBERS);
    }

    /**
     * @param int $teamId
     *
     * @return bool
     */
    public function canUpdate(int $teamId): bool
    {
        return $this->canStore($teamId);
    }
}
