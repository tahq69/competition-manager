<?php namespace App\Http\Requests\Competition;

use App\Contracts\ITeamRepository as ITeams;
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
    private $team;

    /**
     * Competition policy constructor.
     *
     * @param \App\Http\Requests\UserRolesPolicy   $user
     * @param \App\Http\Requests\MemberRolesPolicy $member
     * @param \App\Contracts\ITeamRepository       $team
     */
    public function __construct(
        UserRolesPolicy $user,
        MemberRolesPolicy $member,
        ITeams $team
    )
    {
        $this->user = $user;
        $this->member = $member;
        $this->team = $team;
    }

    /**
     * @param  int $teamId Owner team identifier.
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

        $hasCredits = $this->team->hasCredits($teamId);

        if (!$hasCredits) return false;

        return true;
    }
}
