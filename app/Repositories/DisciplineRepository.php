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
    function modelClass(): string
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
        return $this->setWhere('competition_id', $competitionId);
    }
}