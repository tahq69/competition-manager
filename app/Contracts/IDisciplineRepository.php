<?php namespace App\Contracts;

/**
 * Interface IDisciplineRepository
 * @package App\Contracts
 */
interface IDisciplineRepository extends IRepository
{
    /**
     * Filter by competition.
     * @param int $competitionId
     * @return $this
     */
    public function whereCompetition(int $competitionId): IDisciplineRepository;
}