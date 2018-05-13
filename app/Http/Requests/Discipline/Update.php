<?php namespace App\Http\Requests\Discipline;

use App\Discipline;
use App\Http\Requests\FormRequest;
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
     *
     * @return bool
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function authorize(Policy $policy): bool
    {
        /** @var \App\Discipline $discipline */
        $discipline = $this->find('discipline');

        return $policy->canUpdate(
            $discipline->team_id, $discipline->competition_id, $discipline->id
        );
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
