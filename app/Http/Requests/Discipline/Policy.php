<?php namespace App\Http\Requests\Discipline;

use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Http\Requests\MemberRolesPolicy;
use App\Http\Requests\UserRolesPolicy;

/**
 * Class Policy
 *
 * @package App\Http\Requests\Discipline
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
     * @param int $cmId
     *
     * @return bool
     */
    public function canStore(int $teamId, int $cmId): bool
    {
        return $this->canManage($teamId);
    }

    /**
     * @param int $teamId
     * @param int $cmId
     * @param int $disciplineId
     *
     * @return bool
     */
    public function canUpdate(int $teamId, int $cmId, int $disciplineId): bool
    {
        return $this->canManage($teamId);
    }

    private function canManage(int $teamId): bool
    {
        $admin = UserRole::SUPER_ADMIN;
        $manage = MemberRole::MANAGE_COMPETITION_DISCIPLINES;

        if (!$this->user->authorized()) return false;
        if ($this->user->hasRole($admin)) return true;

        if (!$this->member->isManager($teamId)) return false;

        return $this->member->hasRole($teamId, $manage);
    }
}
