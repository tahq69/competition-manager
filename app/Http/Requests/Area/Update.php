<?php namespace App\Http\Requests\Area;

use App\Area;
use App\Contracts\IAreaRepository;
use App\Rules\AlphaDashSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Update
 *
 * @package App\Http\Requests\Area
 */
class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Area\Policy $policy
     * @param \App\Contracts\IAreaRepository $areas
     *
     * @return bool Is user authorized to make this request.
     */
    public function authorize(Policy $policy, IAreaRepository $areas): bool
    {
        $areaId = $this->route('area');

        /** @var \App\Area $area */
        $area = $areas->find($areaId);

        return $policy->canUpdate($area);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array Rules to validate request.
     */
    public function rules(): array
    {
        $areaId = $this->route('area');
        $cmId = $this->route('competition');

        return [
            'title' => [
                'required', 'min:3', 'max:255', new AlphaDashSpace,
                // Title should be unique for current competition scope.
                Rule::unique('areas', 'title')->where('competition_id', $cmId)->ignore($areaId),
            ],
            'type' => ['required', Rule::in(Area::TYPES)],
            'nr' => [
                'required', 'integer',
                // Nr. should be unique for current competition scope.
                Rule::unique('areas', 'nr')->where('competition_id', $cmId)->ignore($areaId),
            ],
            'description' => [],
        ];
    }
}
