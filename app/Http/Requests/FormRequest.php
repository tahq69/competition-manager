<?php namespace App\Http\Requests;

use App\Providers\BindingServiceProvider;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

/**
 * Class FormRequest
 *
 * @package App\Http\Requests
 */
class FormRequest extends LaravelFormRequest
{
    private $models = [];

    /**
     * @param string $routeParam
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function find(string $routeParam)
    {
        if (!array_key_exists($routeParam, $this->models)) {
            $id = $this->route($routeParam);
            $plural = str_plural($routeParam);
            $repository = BindingServiceProvider::resolveRepository($plural);

            $this->models[$routeParam] = $repository->find($id);
        }

        return $this->models[$routeParam];
    }
}
