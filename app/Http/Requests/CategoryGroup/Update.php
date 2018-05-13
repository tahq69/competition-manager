<?php namespace App\Http\Requests\CategoryGroup;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Update
 *
 * @package App\Http\Requests\CategoryGroup
 */
class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\CategoryGroup\Policy $policy
     *
     * @return bool
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function authorize(Policy $policy): bool
    {
        /** @var \App\CategoryGroup $group */
        $group = $this->find('group');

        return $policy->canUpdate(
            $group->team_id, $group->competition_id, $group->id
        );
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
            'id' => [
                'required', 'integer',
                Rule::exists('category_groups', 'id'),
                Rule::in($groupId),
            ],
            'competition_id' => [
                'required', 'integer',
                Rule::exists('competitions', 'id'),
                Rule::in($cmId),
            ],
            'discipline_id' => [
                'required', 'integer',
                Rule::exists('disciplines', 'id'),
                Rule::in($disciplineId),
            ],
            'title' => [
                'required', 'min:3', 'max:255',
                // Title should be unique for current discipline, except itself.
                Rule::unique('category_groups', 'title')
                    ->where('discipline_id', $disciplineId)
                    ->ignore($groupId),
            ],
            'short' => [
                'required', 'min:3', 'max:15',
                // Short title should be unique for current discipline, except
                // itself.
                Rule::unique('category_groups', 'short')
                    ->where('discipline_id', $disciplineId)
                    ->ignore($groupId),
            ],
            'rounds' => ['required', 'integer', 'min:0'],
            'time' => ['required', 'integer', 'min:0'],
            'min' => ['required', 'integer', 'min:0'],
            'max' => ['required', 'integer', 'min:0'],
        ];
    }
}
