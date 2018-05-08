<?php namespace App\Http\Requests\TeamMemberRoles;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Index
 *
 * @package App\Http\Requests\TeamMemberRoles
 */
class Index extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\TeamMemberRoles\Policy $policy
     *
     * @return bool
     */
    public function authorize(Policy $policy): bool
    {
        $teamId = $this->route('team');
        $memberId = $this->route('member');

        return $policy->canList($teamId, $memberId);
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
