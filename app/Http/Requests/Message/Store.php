<?php namespace App\Http\Requests\Message;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Store
 *
 * @package App\Http\Requests\Message
 */
class Store extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subject' => ['required'],
            'body' => ['required'],
            'importance_level' => [Rule::in(range(1, 10))],
            'to' => ['required', Rule::exists('users', 'id')],
        ];
    }
}
