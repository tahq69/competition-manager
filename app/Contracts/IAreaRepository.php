<?php namespace App\Contracts;

/**
 * Interface IAreaRepository
 * @package App\Contracts
 */
interface IAreaRepository extends IRepository
{
    /**
     * Filter by competition.
     * @param  int $competitionId
     * @return $this
     */
    public function whereCompetition(int $competitionId): IAreaRepository;
}
