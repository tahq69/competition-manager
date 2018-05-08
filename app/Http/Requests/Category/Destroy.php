<?php namespace App\Http\Requests\Category;

use App\Contracts\ICategoryRepository as ICategories;
use Illuminate\Foundation\Http\FormRequest;

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
     * @param \App\Contracts\ICategoryRepository $categories
     *
     * @return bool
     */
    public function authorize(Policy $policy, ICategories $categories): bool
    {
        $catId = $this->route('category');

        /** @var \App\Category $cat */
        $cat = $categories->find($catId, ['id', 'competition_id', 'team_id']);

        return $policy->canDelete($cat->team_id, $cat->competition_id, $catId);
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
