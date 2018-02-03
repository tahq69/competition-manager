<?php namespace App\Http\Requests\CategoryGroup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Destroy
 * @package App\Http\Requests\CategoryGroup
 */
class Destroy extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @param  Policy $policy
     * @return bool
     */
    public function authorize(Policy $policy)
    {
        return $policy->canDelete($this->route('competition'));
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [];
    }
}