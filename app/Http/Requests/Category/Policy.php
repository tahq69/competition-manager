<?php namespace App\Http\Requests\Category;

use App\Http\Requests\CompetitionManagePolicy;

/**
 * Class Policy
 * @package App\Http\Requests\Category
 */
class Policy
{
    /**
     * @var CompetitionManagePolicy
     */
    private $policy;

    /**
     * Policy constructor.
     * @param CompetitionManagePolicy $policy
     */
    public function __construct(CompetitionManagePolicy $policy)
    {
        $this->policy = $policy;
    }

    /**
     * @param  int $competitionId
     * @return bool
     */
    public function canStore(int $competitionId): bool
    {
        return $this->policy->isManager($competitionId);
    }

    /**
     * @param  int $competitionId
     * @return bool
     */
    public function canUpdate(int $competitionId): bool
    {
        return $this->policy->isManager($competitionId);
    }

    /**
     * @param  int $competitionId
     * @return bool
     */
    public function canDelete(int $competitionId): bool
    {
        return $this->policy->isManager($competitionId);
    }
}
