<?php namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class IRepository
 * @package App\Contracts
 */
interface IRepository
{
    /**
     * Get the table associated with the repository model.
     * @return string
     */
    public function getTable();

    /**
     * Get current repository full model class name
     * @return string
     */
    public function modelClass(): string;

    /**
     * Set repository queryable ordering
     * @param string $by
     * @param string $direction
     * @return $this
     */
    public function orderBy($by = 'id', $direction = 'desc');

    /**
     * Set repository queryable ordering from a request and sort column mapping
     * array.
     * @param \Illuminate\Http\Request $request
     * @param array $mapping
     * @param string $defaultOrder
     * @param string $defaultDirection
     * @return $this
     */
    public function setupOrdering(
        \Illuminate\Http\Request $request,
        array $mapping,
        $defaultOrder = 'id',
        $defaultDirection = 'desc'
    );

    /**
     * Find single instance of model
     * @param * $id
     * @param array $columns
     * @return Model
     */
    public function find($id, array $columns = ['*']);

    /**
     * Get collection of models
     * @param array $columns
     * @param array $filters
     * @return Collection
     */
    public function get(array $columns = ['*'], array $filters = []);

    /**
     * Create new instance in of model in database
     * @param array $input
     * @return Model
     */
    public function create(array $input);

    /**
     * Update existing instance in database
     * @param array $input
     * @param int $id
     * @param Model $model
     * @return Model
     */
    public function update(array $input, $id, Model $model = null);

    /**
     * Delete record in database
     * @param int $id
     * @return boolean
     */
    public function delete($id): bool;

    /**
     * Get count of querable records
     * @return integer
     */
    public function count(): int;
}
