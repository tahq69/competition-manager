<?php namespace App\Http\Requests\Discipline;

use App\Contracts\ICompetitionRepository as ICompetitions;
use App\Http\Requests\UserRolesPolicy;
use App\Role;
use Auth;

/**
 * Class Policy
 * @package App\Http\Requests\Discipline
 */
class Policy
{
    /**
     * @param  ICompetitions $competitions
     * @param  int $competitionId
     * @return bool
     */
    public static function canStore(
        ICompetitions $competitions,
        int $competitionId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $roles = UserRolesPolicy::roles($user);

        // Super Admin can create anything and for anyone.
        if (UserRolesPolicy::hasRole($roles, Role::SUPER_ADMIN)) return true;

        $competition = $competitions
            ->filterOwnedOrManaged()
            ->find($competitionId);

        // If current user is competition manager or owner - he can edit/create
        // disciplines for this competition.
        if ($competition) return true;

        return false;
    }

    /**
     * @param ICompetitions $competitions
     * @param int $competitionId
     * @return bool
     */
    public static function canUpdate(
        ICompetitions $competitions, int $competitionId): bool
    {
        return Policy::canStore($competitions, $competitionId);
    }
}
