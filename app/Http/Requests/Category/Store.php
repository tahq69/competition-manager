<?php namespace App\Http\Requests\Category;

use App\Category;
use App\Contracts\ICategoryGroupRepository as IGroups;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Store
 *
 * @package App\Http\Requests\Category
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Category\Policy      $policy
     * @param \App\Contracts\ICategoryGroupRepository $groups
     *
     * @return bool
     */
    public function authorize(Policy $policy, IGroups $groups): bool
    {
        $groupId = $this->route('group');

        /** @var \App\CategoryGroup $group */
        $group = $groups->find($groupId, ['id', 'competition_id', 'team_id']);
        $teamId = $group->team_id;
        $cmId = $group->competition_id;

        return $policy->canStore($teamId, $cmId, $groupId);
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

        return [
            'competition_id' => [
                'required', 'numeric',
                // Competition id should exists in database.
                Rule::exists('competitions', 'id'),
                // Url competition id should be same as body identifier.
                Rule::in($cmId),
            ],
            'discipline_id' => [
                'required', 'numeric',
                // Discipline id should exists in database.
                Rule::exists('disciplines', 'id'),
                // Url discipline id should be same as body identifier.
                Rule::in($disciplineId),
            ],
            'category_group_id' => [
                'required', 'numeric',
                // Group id should exists in database.
                Rule::exists('category_groups', 'id'),
                // Url group id should be same as body identifier.
                Rule::in($groupId),
            ],
            'area_id' => [
                'required', 'numeric',
                // Area id should exists in database for current competition.
                Rule::exists('areas', 'id')
                    ->where('competition_id', $cmId),
            ],
            'title' => [
                'required', 'min:3', 'max:255',
                // Title should be unique for current discipline.
                Rule::unique('categories', 'title')
                    ->where('discipline_id', $disciplineId),
            ],
            'short' => [
                'required', 'min:3', 'max:15',
                // Short title should be unique for current discipline.
                Rule::unique('categories', 'short')
                    ->where('discipline_id', $disciplineId),
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
