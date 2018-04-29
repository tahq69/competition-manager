<?php namespace App\Http\Requests\Competition;

use App\Contracts\ICompetitionRepository;
use App\Contracts\ITeamRepository;
use App\Contracts\MemberRole;
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
     */
    public function canStore(int $teamId): bool
    {
        if (!$this->user->authorized()) return false;

        $userId = $this->user->id;

        // Allow to create team competition only if user has required role for
        // it and team has credits.
        $canCreate = $this->member->hasRole(
            $teamId, $userId, MemberRole::CREATE_COMPETITIONS
        );

        if (!$canCreate) return false;

        $hasCredits = $this->teams->hasCredits($teamId);

        if (!$hasCredits) return false;

        return true;
    }

    /**
     * @param int $cmId
     * @param int $teamId
     *
     * @return bool
     * @throws \App\Exceptions\CompetitionCompletedException
     */
    public function canUpdate(int $cmId, int $teamId)
    {
        if (!$this->user->authorized()) return false;
        $userId = $this->user->id;
        $isManager = $this->member->isManager($teamId, $userId);

        if (!$isManager) return false;

        $canManage = $this->member->hasRole(
            $teamId, $userId, MemberRole::MANAGE_COMPETITIONS
        );
        if (!$canManage) return false;

        /** @var \App\Competition $cm */
        $cm = $this->competitions->find($cmId);
        $cm->ensureIsEditable();

        return true;
    }
}
