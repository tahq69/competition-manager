<?php namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Store
 * @package App\Http\Requests\Team
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
        return $policy->canStore();
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required', 'min:3', 'max:255',
                // Name should be unique in a system.
                Rule::unique('teams', 'name'),
            ],
            'short' => [
                'required', 'min:3', 'max:15',
                // Short title should be unique in a system.
                Rule::unique('teams', 'short'),
            ],
            'logo' => ['max:1000', 'url'],
        ];
    }
}