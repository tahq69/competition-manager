<?php namespace App\Http\Requests\Competition;

use App\Competition;
use App\Contracts\ICompetitionRepository;
use App\Contracts\ITeamRepository;
use App\Contracts\MemberRole;
use App\Contracts\UserRole;
use App\Http\Requests\MemberRolesPolicy;
use App\Http\Requests\UserRolesPolicy;

/**
 * Class Policy
 *
 * @package App\Http\Requests\Competition
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
     * @var \App\Contracts\ITeamRepository
     */
    private $teams;

    /**
     * @var \App\Contracts\ICompetitionRepository
     */
    private $competitions;

    /**
     * Competition policy constructor.
     *
     * @param \App\Http\Requests\UserRolesPolicy    $userRoles
     * @param \App\Http\Requests\MemberRolesPolicy  $memberRoles
     * @param \App\Contracts\ITeamRepository        $teams
     * @param \App\Contracts\ICompetitionRepository $competitions
     */
    public function __construct(
        UserRolesPolicy $userRoles,
        MemberRolesPolicy $memberRoles,
        ITeamRepository $teams,
        ICompetitionRepository $competitions
    )
    {
        $this->user = $userRoles;
        $this->member = $memberRoles;
        $this->teams = $teams;
        $this->competitions = $competitions;
    }

    /**
     * @param int $teamId Owner team identifier.
     *
     * @return bool
     * @throws \App\Exceptions\TeamOutOfCreditsException
     */
    public function canStore(int $teamId): bool
    {
        $admin = UserRole::SUPER_ADMIN;
        $createCompetitions = MemberRole::CREATE_COMPETITIONS;

        if (!$this->user->authorized()) return false;
        if ($this->user->hasRole($admin)) return true;

        // Allow to create team competition only if user has required role for
        // it and team has credits.
        if (!$this->member->hasRole($teamId, $createCompetitions)) return false;

        /** @var \App\Team $team */
        $team = $this->teams->find($teamId);
        $team->ensureHasCredits();

        return true;
    }

    /**
     * @param \App\Competition $cm
     *
     * @return bool
     * @throws \App\Exceptions\CompetitionCompletedException
     */
    public function canUpdate(Competition $cm)
    {
        $admin = UserRole::SUPER_ADMIN;
        $manageCompetitions = MemberRole::MANAGE_COMPETITIONS;
        $teamId = $cm->team_id;

        if (!$this->user->authorized()) return false;
        if ($this->user->hasRole($admin)) return true;

        if (!$this->member->isManager($teamId)) return false;
        if (!$this->member->hasRole($teamId, $manageCompetitions)) return false;

        $cm->ensureIsEditable();

        return true;
    }
}
