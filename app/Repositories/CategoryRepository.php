<?php namespace App\Repositories;

use App\Category;
use App\Contracts\ICategoryRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CategoryRepository
 * @package App\Repositories
 */
class CategoryRepository extends Repository implements ICategoryRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass(): string
    {
        return Category::class;
    }

    /**
     * Filter by competition.
     * @param  int $competitionId
     * @return $this
     */
    public function whereCompetition(int $competitionId): ICategoryRepository
    {
        return $this->setWhere('competition_id', $competitionId);
    }

    /**
     * Filter by discipline.
     * @param  int $disciplineId
     * @return $this
     */
    public function whereDiscipline(int $disciplineId): ICategoryRepository
    {
        return $this->setWhere('discipline_id', $disciplineId);
    }

    /**
     * Filter by category group.
     * @param  int $groupId
     * @return $this
     */
    public function whereGroup(int $groupId): ICategoryRepository
    {
        return $this->setWhere('category_group_id', $groupId);
    }

    /**
     * Sort records by they order value.
     * @return $this
     */
    public function sortByOrder(): ICategoryRepository
    {
        return $this->setQuery(function (Builder $query) {
            return $query->orderBy('order');
        });
    }
}