<?php namespace App\Repositories;

use App\Contracts\IRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class Repository
 *
 * @package App\Repositories
 */
abstract class Repository implements IRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var  Builder
     */
    protected $query;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->model = app($this->modelClass());
    }

    /**
     * Get the table associated with the repository model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * Get current repository full model class name
     *
     * @return string
     */
    abstract function modelClass(): string;

    /**
     * Set repository queryable ordering
     *
     * @param string $by
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($by = 'id', $direction = 'desc')
    {
        $this->query = $this->getQuery()->orderBy($by, $direction);

        return $this;
    }

    /**
     * Set repository queryable ordering from a request and sort column mapping
     * array.
     *
     * @param \Illuminate\Http\Request $request
     * @param array                    $mapping
     * @param string                   $defaultOrder
     * @param string                   $defaultDirection
     *
     * @return $this
     */
    public function setupOrdering(
        \Illuminate\Http\Request $request,
        array $mapping,
        $defaultOrder = 'id',
        $defaultDirection = 'desc'
    )
    {
        $directions = ['ascending' => 'asc', 'descending' => 'desc'];
        $long_directions = array_keys($directions);
        $short_directions = array_values($directions);

        $by = $defaultOrder;
        $direction = $defaultDirection;

        if (array_key_exists($request->sort_by, $mapping)) {
            $by = $mapping[$request->sort_by];
        }

        if (in_array($request->sort_direction, $long_directions)) {
            $direction = $directions[$request->sort_direction];
        }

        if (in_array($request->sort_direction, $short_directions)) {
            $direction = $request->sort_direction;
        }

        $this->orderBy($by, $direction);

        return $this;
    }

    /**
     * Find single instance of model
     *
     * @param * $id
     * @param array $columns
     *
     * @return Model
     */
    public function find($id, array $columns = ['*'])
    {
        $result = $this->getQuery()->findOrFail($id, $columns);

        $this->resetQuery();

        return $result;
    }

    /**
     * Get collection of models
     *
     * @param array $columns
     * @param array $filters
     *
     * @return Collection
     * @throws \Exception
     */
    public function get(array $columns = ['*'], array $filters = [])
    {
        $this->filter($filters);

        $result = $this->getQuery()->get($columns);

        $this->resetQuery();

        return $result;
    }

    /**
     * Create new instance in of model in database
     *
     * @param array $input
     *
     * @return Model
     * @throws \Throwable
     */
    public function create(array $input)
    {
        $model = $this->model->newInstance($input);

        $model->saveOrFail();

        return $model;
    }

    /**
     * Update existing instance in database
     *
     * @param array $input
     * @param int   $id
     * @param Model $model
     *
     * @return Model
     */
    public function update(array $input, $id, Model $model = null)
    {
        if (!$model) {
            $model = $this->find($id);
        }

        $model->update($input);

        $this->resetQuery();

        return $model;
    }

    /**
     * Delete record in database
     *
     * @param int $id
     *
     * @return boolean
     * @throws \Exception
     */
    public function delete($id): bool
    {
        return $this->find($id)->delete();
    }

    /**
     * Get count of queryable records
     *
     * @param  bool $reset
     *
     * @return integer
     */
    public function count(bool $reset = true): int
    {
        $result = $this->getQuery()->count();

        if ($reset) $this->resetQuery();

        return $result;
    }

    /**
     * Set filter params to querable
     *
     * @param array $filters
     *
     * @return $this
     * @throws \Exception
     */
    protected function filter($filters = [])
    {
        foreach ($filters as $filter => $index) {
            if (is_array($filter)) {
                $this->query = call_user_func_array(
                    [$this->getQuery(), 'where'], $filter
                );
            } else {
                $type = gettype($filter);
                throw new \Exception(
                    "Filters property should be array with arrays, but got '$type' at position '$index'"
                );
            }
        }

        return $this;
    }

    /**
     * Get actual query
     *
     * @return Builder
     */
    protected function getQuery(): Builder
    {
        if (!$this->query) {
            $this->query = $this->model->newQuery();
        }

        return $this->query;
    }

    /**
     * Update actual query with new operation.
     *
     * @param  callable $queryFunction
     *
     * @return $this
     */
    protected function setQuery(callable $queryFunction)
    {
        $this->query = $queryFunction($this->getQuery());
        return $this;
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param  mixed $relations
     *
     * @return $this
     */
    protected function with($relations)
    {
        return $this->setQuery(function (Builder $query) use ($relations) {
            return $query->with($relations);
        });
    }

    /**
     * Set where statement on current query.
     *
     * @param  string|array|\Closure $column
     * @param  string                $operator
     * @param  mixed                 $value
     * @param  string                $boolean
     *
     * @return $this
     */
    protected function setWhere($column, $operator = null, $value = null,
                                $boolean = 'and')
    {
        return $this->setQuery(function (Builder $q) use (
            $column, $operator, $value, $boolean
        ) {
            return $q->where($column, $operator, $value, $boolean);
        });
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  string $column
     * @param  mixed  $values
     * @param  string $boolean
     * @param  bool   $not
     *
     * @return $this
     */
    protected function setWhereIn(string $column, $values, $boolean = 'and',
                                  $not = false)
    {
        return $this->setQuery(function (Builder $q) use (
            $column, $values, $boolean, $not
        ) {
            return $q->whereIn($column, $values, $boolean, $not);
        });
    }

    /**
     * Reset current query to new instance
     */
    protected function resetQuery()
    {
        $this->query = $this->model->newQuery();
    }
}
