<?php namespace App\Http\Requests\CategoryGroup;

use App\Http\Requests\FormRequest;
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
     *
     * @return bool
     * @throws \App\Exceptions\RouteBindingOverlapException
     */
    public function authorize(Policy $policy): bool
    {
        /** @var \App\Discipline $discipline */
        $discipline = $this->find('discipline');

        return $policy->canStore(
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