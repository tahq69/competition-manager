<?php namespace App\Http\Requests\Area;

use App\Area;
use App\Contracts\IAreaRepository as IAreas;
use App\Http\Requests\FormRequest;

/**
 * Class Destroy
 *
 * @package App\Http\Requests\Area
 */
class Destroy extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \App\Http\Requests\Area\Policy $policy
     *
     * @return bool
     * @throws \Exception
     */
    public function authorize(Policy $policy): bool
    {
        /** @var \App\Area $area */
        $area = $this->resolve('area');

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
