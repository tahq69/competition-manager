<?php namespace App\Http\Requests\TeamMembers;

use App\Contracts\ITeamMemberRepository as IMembers;
use App\Http\Requests\UserRolesPolicy;
use App\Role;
use App\TeamMember;

/**
 * Class Policy
 * @package App\Http\Requests\TeamMembers
 */
class Policy
{
    /**
     * @var IMembers
     */
    private $members;

    /**
     * @var UserRolesPolicy
     */
    private $user;

    /**
     * Policy constructor.
     * @param IMembers $members
     * @param UserRolesPolicy $user
     */
    public function __construct(IMembers $members, UserRolesPolicy $user)
    {
        $this->members = $members;
        $this->user = $user;
    }

    /**
     * @param int $teamId
     * @return bool
     */
    public function canStore(int $teamId): bool
    {
        if (!$this->user->authorized()) return false;

        // Super Admin can create anything and for anyone.
        if ($this->user->hasRole(Role::SUPER_ADMIN)) return true;

        $isManager = $this->members
            ->filterByTeam($teamId)
            ->filterByUser($this->user->id)
            ->filterByMembership(TeamMember::MANAGER)
            ->count();

        // If current user is team manager - he can edit team members.
        if ($isManager > 0) return true;

        return false;
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
