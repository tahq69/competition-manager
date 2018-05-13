<?php namespace App\Http\Requests\TeamMembers;

use App\Http\Requests\FormRequest;
use App\TeamMember;
use Illuminate\Validation\Rule;

/**
 * Class Store
 *
 * @package App\Http\Requests\TeamMembers
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\TeamMembers\Policy $policy
     *
     * @return bool
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function authorize(Policy $policy)
    {
        /** @var \App\Team $team */
        $team = $this->find('team');

        return $policy->canStore($team->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:255'],
            'user_id' => ['required', 'integer',],
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
                ->where('membership_type', TeamMember::MEMBER),
        ];

        $v->sometimes('user_id', $rules, function ($input) {
            return $input->has('user_id') && $input->user_id > 0;
        });

        return $v;
    }
}
