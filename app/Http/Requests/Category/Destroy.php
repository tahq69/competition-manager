<?php namespace App\Http\Requests\Category;

use App\Http\Requests\FormRequest;

/**
 * Class Destroy
 *
 * @package App\Http\Requests\Category
 */
class Destroy extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Category\Policy $policy
     *
     * @return bool
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function authorize(Policy $policy): bool
    {
        /** @var \App\Category $category */
        $category = $this->find('category');

        return $policy->canDelete(
            $category->team_id, $category->competition_id, $category->id
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
