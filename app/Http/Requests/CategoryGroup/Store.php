<?php namespace App\Http\Requests\CategoryGroup;

use App\Contracts\IDisciplineRepository as IDisciplines;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Store
 *
 * @package App\Http\Requests\CategoryGroup
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\CategoryGroup\Policy $policy
     * @param \App\Contracts\IDisciplineRepository    $disciplines
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

        return $policy->canStore($teamId, $competitionId, $disciplineId);
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
            'competition_id' => [
                'required', 'integer',
                // Competition id should exists in database.
                Rule::exists('competitions', 'id'),
                // Url competition id should be same as body identifier.
                Rule::in($cmId),
            ],
            'discipline_id' => [
                'required', 'integer',
                // Discipline id should exists in database.
                Rule::exists('disciplines', 'id'),
                // Url discipline id should be same as body identifier.
                Rule::in($disciplineId),
            ],
            'title' => [
                'required', 'min:3', 'max:255',
                // Title should be unique for current discipline.
                Rule::unique('category_groups', 'title')
                    ->where('discipline_id', $disciplineId),
            ],
            'short' => [
                'required', 'min:3', 'max:15',
                // Short title should be unique for current discipline.
                Rule::unique('category_groups', 'short')
                    ->where('discipline_id', $disciplineId),
            ],
            'rounds' => ['required', 'integer', 'min:0'],
            'time' => ['required', 'integer', 'min:0'],
            'min' => ['required', 'integer', 'min:0'],
            'max' => ['required', 'integer', 'min:0'],
        ];
    }
}