<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleMapsUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!(preg_match('/^https:\/\/(www\.)?google\.com\/maps\//', $value) || preg_match('/^https:\/\/maps\.app\.goo\.gl\//', $value))){
            $fail('The :attribute must be a valid Google Maps URL.');
        }
    }
}
