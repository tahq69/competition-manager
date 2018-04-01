<?php namespace App\Http\Requests\Team;

use App\Rules\RelativeUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Update
 * @package App\Http\Requests\Team
 */
class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @param  Policy $policy
     * @return bool
     */
    public function authorize(Policy $policy)
    {
        $teamId = $this->route('team');

        return $policy->canUpdate($teamId);
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $teamId = $this->route('team');

        return [
            'id' => [
                'required', 'integer',
                Rule::exists('teams', 'id'),
                Rule::in($teamId),
            ],
            'name' => [
                'required', 'min:3', 'max:255',
                // Name should be unique in a system.
                Rule::unique('teams', 'name')->ignore($teamId),
            ],
            'short' => [
                'required', 'min:3', 'max:15',
                // Short title should be unique in a system.
                Rule::unique('teams', 'short')->ignore($teamId),
            ],
            'logo' => ['required', new RelativeUrl, 'max:1000'],
        ];
    }
}