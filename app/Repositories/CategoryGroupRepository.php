<?php namespace App\Repositories;

use App\CategoryGroup;
use App\Contracts\ICategoryGroupRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CategoryGroupRepository
 * @package App\Repositories
 */
class CategoryGroupRepository
    extends PaginationRepository
    implements ICategoryGroupRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass(): string
    {
        return CategoryGroup::class;
    }

    /**
     * Filter by competition.
     * @param  int $competitionId
     * @return $this
     */
    public function whereCompetition(int $competitionId): ICategoryGroupRepository
    {
        return $this->setWhere('competition_id', $competitionId);
    }

    /**
     * Filter by discipline.
     * @param  int $disciplineId
     * @return $this
     */
    public function whereDiscipline(int $disciplineId): ICategoryGroupRepository
    {
        return $this->setWhere('discipline_id', $disciplineId);
    }

    /**
     * Sort records by they order value.
     * @return $this
     */
    public function sortByOrder(): ICategoryGroupRepository
    {
        return $this->setQuery(function (Builder $query) {
            return $query->orderBy('order');
        });
    }
}