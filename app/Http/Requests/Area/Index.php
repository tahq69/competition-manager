<?php namespace App\Http\Requests\Area;

use App\Http\Requests\FormRequest;

/**
 * Class Index
 *
 * @package App\Http\Requests\Area
 */
class Index extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // anyone can access competition area list.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sort_by' => 'alpha_dash',
            'sort_direction' => 'alpha_dash',
        ];
    }
}
