<?php namespace App\Contracts;

/**
 * Interface ICategoryGroupRepository
 *
 * @package App\Contracts
 */
interface ICategoryGroupRepository extends IRepository
{
    /**
     * Filter by competition.
     *
     * @param  int $competitionId
     *
     * @return $this
     */
    public function whereCompetition(int $competitionId): ICategoryGroupRepository;

    /**
     * Filter by discipline.
     *
     * @param  int $disciplineId
     *
     * @return $this
     */
    public function whereDiscipline(int $disciplineId): ICategoryGroupRepository;

    /**
     * Sort records by they order value.
     *
     * @return $this
     */
    public function sortByOrder(): ICategoryGroupRepository;

    /**
     * Join categories to the requested groups.
     *
     * @param array $columns
     *
     * @return $this
     */
    public function withCategories($columns = ['*']): ICategoryGroupRepository;
}