<?php namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Store
 *
 * @package App\Http\Requests\Competition
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param  Policy $policy
     *
     * @return bool
     */
    public function authorize(Policy $policy)
    {
        $teamId = $this->route('team');

        return $policy->canStore($teamId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [
                'required', 'min:3', 'max:255',
                // Competition title should be unique in a system.
                Rule::unique('competitions', 'title'),
            ],
            'subtitle' => [
                'required', 'min:3', 'max:255',
            ],
            'registration_till' => [
                'required', 'date', 'after:tomorrow',
            ],
            'organization_date' => [
                'required', 'date', 'after:registration_till',
            ],
            'team_id' => [
                'required', Rule::exists('teams', 'id'),
            ],
        ];
    }
}
