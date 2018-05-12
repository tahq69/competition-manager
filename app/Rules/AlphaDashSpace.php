<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AlphaDashSpace implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match('/^[\pL\pM\pN\s-_\']+$/u', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.alpha_dash_space');
    }
}
