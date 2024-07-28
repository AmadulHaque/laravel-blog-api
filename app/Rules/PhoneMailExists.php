<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class PhoneMailExists implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        // Check if the value exists in either phone or email column
        return User::where('phone', $value)->orWhere('email', $value)->exists();
    }

    public function message()
    {
        return 'The :attribute does not match any registered phone or email.';
    }
}
