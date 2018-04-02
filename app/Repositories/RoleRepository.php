<?php namespace App\Repositories;

use App\Contracts\IRoleRepository;
use App\Role;

/**
 * Class RoleRepository
 * @package App\Repositories
 */
class RoleRepository extends Repository implements IRoleRepository
{
    /**
     * Get current repository full model class name
     * @return string
     */
    function modelClass(): string
    {
        return Role::class;
    }

    /**
     * Set "where in" clause on the query results.
     * @param  array $roles
     * @return $this
     */
    public function whereKeyIn(array $roles)
    {
        return $this->setWhereIn('key', $roles);
    }
}
