<?php namespace App\Contracts;

/**
 * Interface ICategoryRepository
 * @package App\Contracts
 */
interface ICategoryRepository extends IRepository
{
    /**
     * Filter by competition.
     * @param  int $competitionId
     * @return $this
     */
    public function whereCompetition(int $competitionId): ICategoryRepository;

    /**
     * Filter by discipline.
     * @param  int $disciplineId
     * @return $this
     */
    public function whereDiscipline(int $disciplineId): ICategoryRepository;

    /**
     * Filter by category group.
     * @param  int $groupId
     * @return $this
     */
    public function whereGroup(int $groupId): ICategoryRepository;

    /**
     * Sort records by they order value.
     * @return $this
     */
    public function sortByOrder(): ICategoryRepository;

}