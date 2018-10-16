<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckImageRule implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $type = explode('.',$value)[1];
        $array = ['jpg','png','gif', 'jpeg'];
        if(in_array($type,$array)){
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ảnh phải có định dạng: jpg,png,gif';
    }
}
