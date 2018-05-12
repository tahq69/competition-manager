<?php namespace App\Http\Requests\Area;

use App\Area;
use App\Competition;
use App\Contracts\ICompetitionRepository as ICompetitions;
use App\Rules\AlphaDashSpace;
use App\Team;
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
     * @var null|\App\Competition
     */
    private $cm = null;

    /**
     * Get request competition model.
     *
     * @return \App\Competition
     */
    public function getCompetition(): Competition
    {
        if (is_null($this->cm)) {
            $cmId = $this->route('competition');
            $this->cm = app(ICompetitions::class)->find($cmId);
        }

        return $this->cm;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Area\Policy $policy
     *
     * @return bool
     */
    public function authorize(Policy $policy): bool
    {
        $cm = $this->getCompetition();

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
