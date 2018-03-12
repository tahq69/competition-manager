<?php namespace App\Http\Requests\Team;

use App\Contracts\ITeamRepository as ITeams;
use App\Http\Requests\UserRolesPolicy;
use App\Role;

/**
 * Class Policy
 * @package App\Http\Requests\Team
 */
class Policy
{
    /**
     * @var UserRolesPolicy
     */
    private $user;

    /**
     * @var ITeams
     */
    private $teams;

    /**
     * Policy constructor.
     * @param UserRolesPolicy $user
     * @param ITeams $teams
     */
    public function __construct(UserRolesPolicy $user, ITeams $teams)
    {
        $this->user = $user;
        $this->teams = $teams;
    }

    /**
     * @return bool
     */
    public function canStore(): bool
    {
        if (!$this->user->authorized()) return false;

        // Allow to create team only if user is super admin or has role allowing
        // create new teams.
        $roles = [Role::SUPER_ADMIN, Role::CREATE_TEAMS];
        if ($this->user->hasAnyRole($roles)) return true;

        return false;
    }

    /**
     * @return bool
     */
    public function canUpdate(int $teamId): bool
    {
        if (!$this->user->authorized()) return false;

        $roles = [Role::SUPER_ADMIN, Role::MANAGE_TEAMS];
        if ($this->user->hasAnyRole($roles)) return true;

        if ($this->teams->isManagerOfTeam(\Auth::user(), $teamId)) {
            return true;
        }

        return false;
    }
}