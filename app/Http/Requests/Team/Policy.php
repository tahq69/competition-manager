<?php namespace App\Http\Requests\Team;

use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Http\Requests\MemberRolesPolicy;
use App\Http\Requests\UserRolesPolicy;

/**
 * Class Policy
 * @package App\Http\Requests\Team
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
     * @param \App\Http\Requests\UserRolesPolicy $user
     * @param \App\Http\Requests\MemberRolesPolicy $member
     */
    public function __construct(UserRolesPolicy $user, MemberRolesPolicy $member)
    {
        $this->user = $user;
        $this->member = $member;
    }

    /**
     * @return bool
     */
    public function canStore(): bool
    {
        if (!$this->user->authorized()) return false;

        // Allow to create team only if user is super admin or has role allowing
        // create new teams.
        if ($this->user->hasRole(UserRole::CREATE_TEAMS)) return true;

        return false;
    }

    /**
     * @param  int $teamId
     * @return bool
     */
    public function canUpdate(int $teamId): bool
    {
        if (!$this->user->authorized()) return false;
        $user = $this->user->id;

        // Super admin or team creator can edit any team details/members/roles.
        if ($this->user->hasRole(UserRole::CREATE_TEAMS)) return true;

        // If authenticated user is manager of the team, allow this action.
        if ($this->member->isManager($teamId, $user)) return true;

        // Only simple members requires roles to access team member data.
        return $this->member->hasRole($teamId, $user, MemberRole::MANAGE_TEAMS);
    }
}
