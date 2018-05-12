<?php namespace App\Http\Requests\Area;

use App\Area;
use App\Contracts\IAreaRepository as IAreas;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Destroy
 *
 * @package App\Http\Requests\Area
 */
class Destroy extends FormRequest
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

        return $policy->canDestroy(
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
        return [];
    }
}
