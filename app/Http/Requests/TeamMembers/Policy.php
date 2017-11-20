<?php namespace App\Http\Requests\TeamMembers;

use App\Contracts\ITeamMemberRepository;
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
     * @param ITeamMemberRepository $members
     * @param int $teamId
     * @return bool
     */
    public static function canStore(
        ITeamMemberRepository $members, int $teamId): bool
    {
        if (!\Auth::check()) {
            return false;
        }

        $user = \Auth::user();
        $roles = UserRolesPolicy::roles($user);

        // Super Admin can create anything and for anyone.
        if (UserRolesPolicy::hasRole($roles, Role::SUPER_ADMIN)) return true;

        $isManager = $members
            ->filterByTeam($teamId)
            ->filterByUser($user->id)
            ->filterByMembership(TeamMember::MANAGER)
            ->count();

        // If current user is team manager - he can edit team members.
        if ($isManager > 0) return true;

        return false;
    }

    /**
     * @param ITeamMemberRepository $members
     * @param int $teamId
     * @return bool
     */
    public static function canUpdate(
        ITeamMemberRepository $members, int $teamId): bool
    {
        return Policy::canStore($members, $teamId);
    }
}
