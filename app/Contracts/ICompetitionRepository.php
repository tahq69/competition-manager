<?php namespace App\Contracts;

/**
 * Interface ICompetitionRepository
 * @package App\Contracts
 */
interface ICompetitionRepository extends IPaginateRepository
{
    /**
     * Filter owned or managed competitions.
     * @return $this
     */
    public function filterOwnedOrManaged();

    /**
     * Filter competitions created by a team.
     * @param int $teamId
     * @return $this
     */
    public function filterByTeam(int $teamId);
}
