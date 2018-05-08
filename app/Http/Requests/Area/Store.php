<?php namespace App\Http\Requests\Area;

use App\Area;
use App\Contracts\ICompetitionRepository as ICompetitions;
use App\Rules\AlphaDashSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Store
 *
 * @package App\Http\Requests\Area
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Area\Policy        $policy
     * @param \App\Contracts\ICompetitionRepository $competitions
     *
     * @return bool
     */
    public function authorize(Policy $policy, ICompetitions $competitions): bool
    {
        $competitionId = $this->route('competition');

        /** @var \App\Competition $cm */
        $cm = $competitions->find($competitionId, ['id', 'team_id']);

        return $policy->canStore($cm->team_id, $cm->id);
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
            'competition_id' => [
                'required', 'integer',
                // Competition identifier should exist in system.
                Rule::exists('competitions', 'id'),
                // Request identifier should be same as URL value.
                Rule::in($cmId),
            ],
            'title' => [
                'required', 'min:3', 'max:255', new AlphaDashSpace,
                // Title should be unique for current competition scope.
                Rule::unique('areas', 'title')->where('competition_id', $cmId),
            ],
            'type' => ['required', Rule::in(Area::TYPES)],
            'nr' => [
                'required', 'integer',
                // Nr. should be unique for current competition scope.
                Rule::unique('areas', 'nr')->where('competition_id', $cmId),
            ],
            'description' => [],
        ];
    }
}
