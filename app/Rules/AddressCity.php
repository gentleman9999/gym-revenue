<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Validation\AddressValidation;
use Illuminate\Contracts\Validation\InvokableRule;

class AddressCity implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $address_validation = session()->get('address_validation');
        if (is_null($address_validation)) {
            $address_validation = AddressValidation::validate(
                request()->getRequestUri(),
                request()->all()
            );

            session()->put('address_validation', $address_validation);
        }

        if (array_key_exists('city', $address_validation['errors'])) {
            $fail($address_validation['errors']['city']);
        }
    }
}
