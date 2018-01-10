<?php namespace App\Repositories;

use App\Competition;
use App\Contracts\ICompetitionRepository;
use Auth;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CompetitionRepository
 * @package App\Repositories
 */
class CompetitionRepository extends PaginationRepository implements ICompetitionRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass(): string
    {
        return Competition::class;
    }

    /**
     * Filter owned or managed competitions
     * @return $this
     */
    public function filterOwnedOrManaged(): ICompetitionRepository
    {
        if (!Auth::check()) {
            return $this;
        }

        $this->setQuery(function (Builder $query) {
            return $query
                ->whereHas('team', function (Builder $q) {
                    $q->whereHas('managers', function (Builder $subQ) {
                        $subQ->where('user_id', Auth::id());
                    });
                })
                ->orWhere('created_by', Auth::id())
                ->orWhere('judge_id', Auth::id());
        });

        return $this;
    }
}
