<?php namespace App\Contracts;

/**
 * Interface ITeamRepository
 * @package App\Contracts
 */
interface ITeamRepository extends IPaginateRepository
{
    /**
     * Filter teams by manager id.
     * @param  int $ownerId
     * @return ITeamRepository
     */
    function filterByManager(int $ownerId): ITeamRepository;

    /**
     * Create new team and attach manager in single transaction.
     * @param  array $input
     * @param  \App\User $owner
     * @return \App\Team
     * @throws \Exception
     */
    function createAndAttachManager(array $input, \App\User $owner): \App\Team;

    /**
     * Crate team member for team.
     * @param  \App\Team $team Team model
     * @param  array $memberDetails
     * @return \App\TeamMember Member model
     */
    public function createMember(\App\Team $team, array $memberDetails): \App\TeamMember;
}