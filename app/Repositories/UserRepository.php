<?php namespace App\Repositories;

use App\Contracts\IUserRepository;
use App\TeamMember;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository
    extends PaginationRepository
    implements IUserRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass()
    {
        return User::class;
    }

    /**
     * Set query to search by name column.
     * @param  string $name
     * @return IUserRepository
     */
    public function searchByName(string $name): IUserRepository
    {
        $this->setQuery(function (Builder $query) use ($name) {
            return $query->where(
                'name', 'LIKE', "%$name%"
            );
        });

        return $this;
    }

    /**
     * Join user roles to the request response
     * @return IUserRepository
     */
    public function withRoles(): IUserRepository
    {
        $this->setQuery(function (Builder $query) {
            return $query->with(['roles' => function (BelongsToMany $q) {
                return $q->select(['key']);
            }]);
        });

        return $this;
    }

    /**
     * Join user teams from membership to the query response.
     * @return IUserRepository
     */
    public function withTeams(): IUserRepository
    {
        $this->query = $this->getQuery()
            ->with(['teams' => function (BelongsToMany $query) {
                $query->getQuery()->select(
                    'teams.id', 'teams.name', 'teams.short', 'teams.logo'
                );
            }]);

        return $this;
    }

    /**
     * Find user and attach user team roles to data.
     * @param $userId
     * @return \App\User
     */
    public function findWithTeamRoles(int $userId)
    {
        /** @var \App\User $user */
        $user = $this->getQuery()->with(['memberships' => function (HasMany $query) {
            $query
                ->with(['roles' => function (BelongsToMany $q) {
                    $q->select('key');
                }])->select(['id', 'user_id', 'team_id']);
        }])->find($userId)->toArray();

        $user['team_roles'] = [];

        collect($user['memberships'])->each(function ($manager) use (&$user) {
            $user['team_roles'][$manager['team_id']] = $manager['roles'];
        });

        unset($user['memberships']);

        return $user;
    }
}
