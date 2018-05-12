<?php namespace App\Http\Requests\Area;

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
     * @var \App\Http\Requests\UserRolesPolicy
     */
    private $user;

    /**
     * @var \App\Http\Requests\MemberRolesPolicy
     */
    private $member;

    /**
     * Area policy constructor.
     *
     * @param \App\Http\Requests\UserRolesPolicy   $userRoles
     * @param \App\Http\Requests\MemberRolesPolicy $memberRoles
     */
    public function __construct(
        UserRolesPolicy $userRoles,
        MemberRolesPolicy $memberRoles)
    {
        $this->user = $userRoles;
        $this->member = $memberRoles;
    }

    /**
     * Determines is the authenticated user able create record.
     *
     * @param int $teamId Team identifier.
     * @param int $cmId   Competition identifier.
     *
     * @return bool
     */
    public function canStore(int $teamId, int $cmId): bool
    {
        return $this->canManage($teamId, $cmId);
    }

    /**
     * Determines is the authenticated user able update provided area record.
     *
     * @param int $teamId Team identifier.
     * @param int $cmId   Competition identifier.
     * @param int $areaId Area identifier.
     *
     * @return bool
     */
    public function canUpdate(int $teamId, int $cmId, int $areaId): bool
    {
        return $this->canManage($teamId, $cmId);
    }

    /**
     * Determines is the authenticated user able destroy provided area record.
     *
     * @param int $teamId Team identifier.
     * @param int $cmId   Competition identifier.
     * @param int $areaId Area identifier.
     *
     * @return bool
     */
    public function canDestroy(int $teamId, int $cmId, int $areaId): bool
    {
        return $this->canManage($teamId, $cmId);
    }

    private function canManage(int $teamId, int $cmId): bool
    {
        $admin = UserRole::SUPER_ADMIN;
        $manageAreas = MemberRole::MANAGE_COMPETITION_AREAS;

        if (!$this->user->authorized()) return false;
        if ($this->user->hasRole($admin)) return true;

        if (!$this->member->isManager($teamId)) return false;
        if (!$this->member->hasRole($teamId, $manageAreas)) return false;

        return true;
    }
}
