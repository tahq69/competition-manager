<?php namespace App\Http\Requests\CategoryGroup;

use App\Http\Requests\FormRequest;

/**
 * Class Destroy
 *
 * @package App\Http\Requests\CategoryGroup
 */
class Destroy extends FormRequest
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

        return $policy->canDelete(
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
        return [];
    }
}