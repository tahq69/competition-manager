<?php namespace App\Repositories;

use App\Area;
use App\Contracts\IAreaRepository;

/**
 * Class AreaRepository
 * @package App\Repositories
 */
class AreaRepository extends Repository implements IAreaRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass(): string
    {
        return Area::class;
    }

    /**
     * Filter by competition.
     * @param  int $competitionId
     * @return $this
     */
    public function whereCompetition(int $competitionId): IAreaRepository
    {
        return $this->setWhere('competition_id', $competitionId);
    }
}
