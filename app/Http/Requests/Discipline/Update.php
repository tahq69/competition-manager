<?php namespace App\Http\Requests\Discipline;

use App\Contracts\IDisciplineRepository as IDisciplines;
use App\Discipline;
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
     * @param \App\Http\Requests\Discipline\Policy $policy
     * @param \App\Contracts\IDisciplineRepository $disciplines
     *
     * @return bool
     */
    public function authorize(Policy $policy, IDisciplines $disciplines): bool
    {
        $disciplineId = $this->route('discipline');
        $discipline = $disciplines->find(
            $disciplineId, ['id', 'competition_id', 'team_id']
        );
        $competitionId = $discipline->competition_id;
        $teamId = $discipline->team_id;

        return $policy->canUpdate($teamId, $competitionId, $disciplineId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $cmId = $this->route('competition');
        $disciplineId = $this->route('discipline');

        return [
            'id' => [
                'required', 'integer',
                Rule::exists('disciplines', 'id'),
                Rule::in($disciplineId),
            ],
            'competition_id' => [
                'required', 'integer',
                // Competition id should exists in database.
                Rule::exists('competitions', 'id'),
                // Url competition id should be same as body identifier.
                Rule::in($cmId),
            ],
            'title' => [
                'required', 'min:3', 'max:255',
                // Title should be unique for current competition.
                Rule::unique('disciplines', 'title')
                    ->where('competition_id', $cmId)
                    ->ignore($disciplineId),
            ],
            'short' => [
                'required', 'min:3', 'max:15',
                // Short title should be unique for current competition.
                Rule::unique('disciplines', 'short')
                    ->where('competition_id', $cmId)
                    ->ignore($disciplineId),
            ],
            'type' => [
                'required', 'min:3', 'max:255',
                Rule::in([Discipline::TYPE_KICKBOXING]),
            ],
            'game_type' => ['required', 'min:3',],
            'description' => ['required', 'min:3',],
        ];
    }
}
