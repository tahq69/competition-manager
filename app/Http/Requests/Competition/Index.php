<?php namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Index
 *
 * @package App\Http\Requests\Competition
 */
class Index extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // anyone can access competition list.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'per_page' => 'integer',
            'page' => 'integer',
            'sort_by' => 'alpha_dash',
            'sort_direction' => 'alpha_dash',
            'owned' => 'boolean',
            'team_id' => 'integer',
        ];
    }
}
