<?php namespace App\Http\Requests\Category;

use App\Category;
use App\Contracts\ICategoryRepository as ICategories;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Update
 *
 * @package App\Http\Requests\Category
 */
class Update extends FormRequest
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
        $teamId = $cat->team_id;
        $cmId = $cat->competition_id;

        return $policy->canUpdate($teamId, $cmId, $catId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $cmId = $this->route('competition');
        $disciplineId = $this->route('discipline');
        $groupId = $this->route('group');
        $categoryId = $this->route('category');

        return [
            'id' => [
                'required', 'integer',
                // Identifier should exists in database.
                Rule::exists('categories', 'id'),
                // Url identifier should be same as request value.
                Rule::in($categoryId),
            ],
            'competition_id' => [
                'required', 'integer',
                // Competition id should exists in database.
                Rule::exists('competitions', 'id'),
                // Url competition id should be same as request value.
                Rule::in($cmId),
            ],
            'discipline_id' => [
                'required', 'integer',
                // Discipline id should exists in database.
                Rule::exists('disciplines', 'id'),
                // Url discipline id should be same as request value.
                Rule::in($disciplineId),
            ],
            'category_group_id' => [
                'required', 'integer',
                // Group id should exists in database.
                Rule::exists('category_groups', 'id'),
                // Url group id should be same as request value.
                Rule::in($groupId),
            ],
            'area_id' => [
                'required', 'integer',
                // Area id should exists in database for current competition.
                Rule::exists('areas', 'id')
                    ->where('competition_id', $cmId),
            ],
            'title' => [
                'required', 'min:3', 'max:255',
                // Title should be unique for current discipline.
                Rule::unique('categories', 'title')
                    ->where('discipline_id', $disciplineId)
                    ->ignore($categoryId),
            ],
            'short' => [
                'required', 'min:3', 'max:15',
                // Short title should be unique for current discipline.
                Rule::unique('categories', 'short')
                    ->where('discipline_id', $disciplineId)
                    ->ignore($categoryId),
            ],
            'display_type' => [
                'required',
                Rule::in(Category::DISPLAY_TYPES),
            ],
            'min' => ['required', 'integer', 'min:0'],
            'max' => ['required', 'integer', 'min:0'],
        ];
    }
}
