<?php namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ViewList
 *
 * @package App\Http\Requests\Team
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
        // anyone can access team details
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
            'per_page' => 'integer',
            'page' => 'integer',
            'sort_by' => 'alpha_dash',
            'sort_direction' => 'alpha_dash',
            'managed' => 'boolean',
        ];
    }
}
