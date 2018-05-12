<?php namespace App\Http\Requests\Area;

use App\Area;
use App\Contracts\IAreaRepository as IAreas;
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
     * @var null|\App\Area
     */
    private $area = null;

    /**
     * Get request area model.
     *
     * @return \App\Area
     */
    public function getArea(): Area
    {
        if (is_null($this->area)) {
            $areaId = $this->route('area');
            $this->area = app(IAreas::class)->find($areaId);
        }

        return $this->area;
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
        $area = $this->getArea();

        return $policy->canUpdate(
            $area->team_id, $area->competition_id, $area->id
        );
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
            'id' => [
                'required', 'integer',
                // Identifier should exist in system.
                Rule::exists('areas', 'id'),
                // Request identifier should be same as URL value.
                Rule::in($areaId),
            ],
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
