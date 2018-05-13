<?php namespace App\Http\Requests\Team;

use App\Http\Requests\FormRequest;
use App\Rules\RelativeUrl;
use Illuminate\Validation\Rule;

/**
 * Class Store
 *
 * @package App\Http\Requests\Team
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Team\Policy $policy
     *
     * @return bool
     */
    public function authorize(Policy $policy): bool
    {
        return $policy->canStore();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
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
            'logo' => ['required', new RelativeUrl, 'max:1000'],
        ];
    }
}
