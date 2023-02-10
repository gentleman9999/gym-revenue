<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Validation\AddressValidation;
use Illuminate\Contracts\Validation\InvokableRule;

class AddressState implements InvokableRule
{
    /**
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed|\Closure $fail
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke($attribute, $value, $fail): void
    {
        $address_validation = session()->get('address_validation');
        if ($address_validation === null) {
            $address_validation = AddressValidation::validate(
                request()->getRequestUri(),
                request()->all()
            );

            session()->put('address_validation', $address_validation);
        }

        if (array_key_exists('state', $address_validation['errors'])) {
            $fail($address_validation['errors']['state']);
        }
    }
}
