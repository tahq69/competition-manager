<?php namespace App\Http\Requests\Area;

use App\Area;
use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Http\Requests\MemberRolesPolicy;
use App\Http\Requests\UserRolesPolicy;

/**
 * Class Policy
 *
 * @package App\Http\Requests\Area
 */
class Policy
{
    /**
     * Area policy constructor.
     *
     * @param \App\Http\Requests\UserRolesPolicy   $userRoles
     * @param \App\Http\Requests\MemberRolesPolicy $memberRoles
     */
    public function __construct(
        UserRolesPolicy $userRoles,
        MemberRolesPolicy $memberRoles
    )
    {
        $this->user = $userRoles;
        $this->member = $memberRoles;
    }

    /**
     * Determines is the authenticated user able create area record.
     *
     * @param int $teamId        Competition owner team identifier.
     * @param int $competitionId Competition identifier.
     *
     * @return bool Is the authenticated user able create area record.
     */
    public function canStore(int $teamId, int $competitionId): bool
    {
        $admin = UserRole::SUPER_ADMIN;
        $manageAreas = MemberRole::MANAGE_COMPETITION_AREAS;

        if (!$this->user->authorized()) return false;
        if ($this->user->hasRole($admin)) return true;

        $userId = $this->user->id;

        if (!$this->member->isManager($teamId, $userId)) return false;
        if (!$this->member->hasRole($teamId, $userId, $manageAreas)) return false;

        return true;
    }

    /**
     * Determines is the authenticated user able update provided area record.
     *
     * @param \App\Area $area Record to be update by the authenticated user.
     *
     * @return bool Is the authenticated user able update provided area record.
     */
    public function canUpdate(Area $area): bool
    {
        $teamId = $area->competition->team_id;
        $competitionId = $area->competition_id;

        return $this->canStore($teamId, $competitionId);
    }
}
