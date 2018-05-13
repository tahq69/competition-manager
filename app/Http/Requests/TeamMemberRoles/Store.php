<?php namespace App\Http\Requests\TeamMemberRoles;

use App\Http\Requests\FormRequest;

/**
 * Class Update
 *
 * @package App\Http\Requests\TeamMemberRoles
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\TeamMemberRoles\Policy $policy
     *
     * @return bool
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function authorize(Policy $policy): bool
    {
        /** @var \App\TeamMember $member */
        $member = $this->find('member');

        return $policy->canUpdate($member->team_id, $member->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'roles' => ['array'],
        ];
    }
}
