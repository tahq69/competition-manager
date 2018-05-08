<?php namespace App\Http\Requests\CategoryGroup;

use App\Contracts\ICategoryGroupRepository as IGroups;
use Illuminate\Foundation\Http\FormRequest;

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
     * @param \App\Contracts\ICategoryGroupRepository $groups
     *
     * @return bool
     */
    public function authorize(Policy $policy, IGroups $groups): bool
    {
        $groupId = $this->route('group');
        /** @var \App\CategoryGroup $group */
        $group = $groups->find($groupId, ['id', 'competition_id', 'team_id']);
        $competitionId = $group->competition_id;
        $teamId = $group->team_id;

        return $policy->canDelete($teamId, $competitionId, $groupId);
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