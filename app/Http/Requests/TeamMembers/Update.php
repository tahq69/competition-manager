<?php namespace App\Http\Requests\TeamMembers;

use App\Contracts\ITeamMemberRepository;
use App\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Update
 *
 * @package App\Http\Requests\TeamMembers
 */
class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\TeamMembers\Policy $policy
     *
     * @return bool
     */
    public function authorize(Policy $policy): bool
    {
        return $policy->canUpdate($this->route('team'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'user_id' => 'required|integer',
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $v = parent::getValidatorInstance();
        $rules = [
            Rule::exists('users', 'id'),
            Rule::unique('team_members', 'user_id')
                ->where('team_id', $this->route('team'))
                ->where('membership_type', TeamMember::MEMBER)
                ->ignore($this->route('member')),
        ];

        $v->sometimes('user_id', $rules, function ($input) {
            return $input->has('user_id') && $input->user_id > 0;
        });

        return $v;
    }
}
