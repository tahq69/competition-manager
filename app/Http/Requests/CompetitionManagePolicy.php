<?php namespace App\Http\Requests;

use App\Contracts\ICompetitionRepository as ICompetitions;
use App\Contracts\UserRole;
use Carbon\Carbon;

/**
 * Class CompetitionManagePolicy
 * @package App\Http\Requests
 */
class CompetitionManagePolicy
{
    /**
     * @var ICompetitions
     */
    private $competitions;

    /**
     * @var UserRolesPolicy
     */
    private $user;

    /**
     * CompetitionManagePolicy constructor.
     * @param ICompetitions $competitions
     * @param UserRolesPolicy $user
     */
    public function __construct(ICompetitions $competitions, UserRolesPolicy $user)
    {
        $this->competitions = $competitions;
        $this->user = $user;
    }

    /**
     * @param  int $competitionId
     * @return bool
     */
    public function isManager(int $competitionId): bool
    {
        if (!$this->user->authorized()) return false;

        // Super Admin can create anything and for anyone.
        if ($this->user->hasRole(UserRole::SUPER_ADMIN)) return true;

        $competition = $this->competitions
            ->filterOwnedOrManaged()
            ->find($competitionId);

        // If current user is competition manager or owner - he can edit/create
        // resources for this competition. But only if date of registration is
        // not already started.
        if ($competition && $competition->registration_till->gt(Carbon::now())) {
            return true;
        }

        return false;
    }
}