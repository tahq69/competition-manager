<?php namespace App\Repositories;

use App\Contracts\ITeamMemberRepository;
use App\TeamMember;
use \Illuminate\Database\Eloquent\Builder;

/**
 * Class TeamMemberRepository
 * @package App\Repositories
 */
class TeamMemberRepository
    extends PaginationRepository
    implements ITeamMemberRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass()
    {
        return TeamMember::class;
    }

    /**
     * Set team id filter on querable
     * @param int $id
     * @return $this
     */
    public function filterByTeam(int $id)
    {
        $this->setQuery(function (Builder $query) use ($id) {
            return $query->where('team_id', $id);
        });

        return $this;
    }

    /**
     * Set user id filter on querable.
     * @param int $id
     * @return $this
     */
    public function filterByUser(int $id)
    {
        $this->setQuery(function (Builder $query) use ($id) {
            return $query->where('user_id', $id);
        });

        return $this;
    }

    /**
     * Set membership type filter on querable.
     * @param string $type
     * @return $this
     */
    public function filterByMembership(string $type)
    {
        $this->setQuery(function (Builder $query) use ($type) {
            return $query->where('membership_type', $type);
        });

        return $this;
    }

    /**
     * Join user details to member
     * @return $this
     */
    public function withUserDetails()
    {
        $this->setQuery(function (Builder $query) {
            return $query->with('user');
        });

        return $this;
    }
}