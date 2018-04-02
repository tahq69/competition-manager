<?php namespace App\Contracts;

/**
 * Interface IRoleRepository
 * @package App\Contracts
 */
interface IRoleRepository extends IRepository
{
    /**
     * Set "where in" clause on the query results.
     * @param  array $roles
     * @return $this
     */
    public function whereKeyIn(array $roles);
}
