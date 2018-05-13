<?php namespace App\Http\Requests\Competition;

use App\Http\Requests\FormRequest;
use App\Rules\AlphaDashSpace;
use Illuminate\Validation\Rule;

/**
 * Class Update
 *
 * @package App\Http\Requests\Competition
 */
class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Competition\Policy $policy
     *
     * @return bool
     * @throws \App\Exceptions\CompetitionCompletedException
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function authorize(Policy $policy): bool
    {
        /** @var \App\Competition $cm */
        $cm = $this->find('competition');

        return $policy->canUpdate($cm);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $cmId = $this->route('competition');

        return [
            'title' => [
                'required', 'min:3', 'max:255', new AlphaDashSpace,
                // Competition title should be unique in a system.
                Rule::unique('competitions', 'title')->ignore($cmId),
            ],
            'subtitle' => [
                'required', 'min:3', 'max:255', new AlphaDashSpace,
            ],
            'registration_till' => [
                'required', 'date', 'after:tomorrow',
            ],
            'organization_date' => [
                'required', 'date', 'after:registration_till',
            ],
            'cooperation' => [],
            'invitation' => [],
            'program' => [],
            'rules' => [],
            'ambulance' => [],
            'prizes' => [],
            'equipment' => [],
            'price' => [],
        ];
    }
}
