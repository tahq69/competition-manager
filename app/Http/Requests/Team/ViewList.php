<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class ViewList extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        // anyone can access team details
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'per_page' => 'number',
            'page' => 'number',
            'sort_by' => 'alpha_dash',
            'sort_direction' => 'alpha_dash',
        ];
    }
}
