<?php namespace App\Repositories;

use App\Contracts\ITeamRepository;
use App\Team;
use App\TeamMember;
use App\User;
use DB;
use Exception;
use Log;

/**
 * Class TeamRepository
 *
 * @package App\Repositories
 */
class TeamRepository extends PaginationRepository implements ITeamRepository
{
    /**
     * Get current repository full model class name.
     *
     * @return string
     */
    function modelClass(): string
    {
        return Team::class;
    }

    /**
     * Filter teams by manager id
     *
     * @param int $ownerId
     *
     * @return ITeamRepository
     */
    function filterByManager(int $ownerId): ITeamRepository
    {
        $this->query = $this->getQuery()
            ->join('team_members', 'team_members.team_id', '=', 'teams.id')
            ->where('team_members.membership_type', TeamMember::MANAGER)
            ->where('team_members.user_id', $ownerId);

        return $this;
    }

    /**
     * Create new team and attach manager in single transaction
     *
     * @param array     $input
     * @param \App\User $owner
     *
     * @return \App\Team
     * @throws Exception
     * @throws \Throwable
     */
    function createAndAttachManager(array $input, \App\User $owner): \App\Team
    {
        DB::beginTransaction();

        try {
            /** @var Team $team */
            $team = $this->create($input);
            $this->createMember($team, [
                'membership_type' => \App\TeamMember::MANAGER,
                'user_id' => $owner->id,
                'name' => $owner->name,
                'team_id' => $team->id,
            ]);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::critical(
                "User '$owner->id' was unavailable store team.",
                [$exception, $input]
            );
            throw new Exception(
                'Internal database transaction error occurred.', 507, $exception
            );
        }

        return $team;
    }

    /**
     * Crate team member for team.
     *
     * @param \App\Team $team
     * @param array     $memberDetails
     *
     * @return \App\TeamMember
     */
    public function createMember(Team $team, array $memberDetails): TeamMember
    {
        /** @var \App\TeamMember $member */
        $member = $team->members()->create($memberDetails);

        return $member;
    }

    /**
     * Determine is the team credits amount greater than a zero.
     *
     * @param int $teamId Team identifier.
     *
     * @return bool Flag indicating whenever team has credits.
     */
    public function hasCredits(int $teamId): bool
    {
        $team = $this->find($teamId, ['id', '_credits']);
        $hasCredits = $team->_credits > 0;

        return $hasCredits;
    }
}
