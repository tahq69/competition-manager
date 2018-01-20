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
    function modelClass(): string
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
        return $this->setWhere('team_id', $id);
    }

    /**
     * Set user id filter on querable.
     * @param int $id
     * @return $this
     */
    public function filterByUser(int $id)
    {
        return $this->setWhere('user_id', $id);
    }

    /**
     * Set membership type filter on querable.
     * @param string $type
     * @return $this
     */
    public function filterByMembership(string $type)
    {
        return $this->setWhere('membership_type', $type);
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