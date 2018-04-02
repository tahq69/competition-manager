<?php namespace App\Http\Requests\TeamMemberRoles;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Update
 * @package App\Http\Requests\TeamMemberRoles
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @param  Policy $policy
     * @return bool
     */
    public function authorize(Policy $policy)
    {
        $teamId = $this->route('team');
        $memberId = $this->route('member');

        return $policy->canUpdate($teamId, $memberId);
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'roles' => ['required', 'array']
        ];
    }
}
