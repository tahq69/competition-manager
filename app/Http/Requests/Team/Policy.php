<?php namespace App\Http\Requests\Team;

use App\Http\Requests\UserRolesPolicy;
use App\Role;

/**
 * Class Policy
 * @package App\Http\Requests\Team
 */
class Policy
{
    /**
     * Policy constructor.
     * @param UserRolesPolicy $user
     */
    public function __construct(UserRolesPolicy $user)
    {
        $this->user = $user;
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
    public function canUpdate(): bool
    {
        return $this->canStore();
    }
}