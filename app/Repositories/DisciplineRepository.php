<?php namespace App\Repositories;

use App\Contracts\IDisciplineRepository;
use App\Discipline;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DisciplineRepository
 * @package App\Repositories
 */
class DisciplineRepository extends Repository implements IDisciplineRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass()
    {
        return Discipline::class;
    }

    /**
     * Filter by competition.
     * @param int $competitionId
     * @return $this
     */
    public function whereCompetition(int $competitionId): IDisciplineRepository
    {
        $this->setQuery(function (Builder $query) use ($competitionId) {
            return $query->where('competition_id', $competitionId);
        });

        return $this;
    }
}