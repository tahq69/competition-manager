<?php namespace App\Http\Requests\Competition;

use App\Contracts\ICompetitionRepository;
use App\Rules\AlphaDashSpace;
use Illuminate\Foundation\Http\FormRequest;
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
     * @param  Policy $policy
     *
     * @return bool
     * @throws \App\Exceptions\CompetitionCompletedException
     */
    public function authorize(Policy $policy, ICompetitionRepository $competitions)
    {
        $cmId = $this->route('competition');

        /** @var \App\Competition $cm */
        $cm = $competitions->find($cmId);

        return $policy->canUpdate($cm);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
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
            'judge_id' => [
                Rule::exists('users', 'id'),
            ],
            'judge_name' => [
                'nullable', 'min:3', 'max:255', new AlphaDashSpace,
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
