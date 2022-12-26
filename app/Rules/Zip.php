<?php

namespace App\Rules;

use App\Domain\Locations\Projections\Location;
use App\Domain\Users\Models\User;
use Illuminate\Contracts\Validation\InvokableRule;
use Johnpaulmedina\Usps\Usps;

class Zip implements InvokableRule
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
        $request = request();
        $requestURI = $request->getRequestUri();
        $entity_id = substr($requestURI, strpos($requestURI, '/', 1) + 1);
        $original = ['address1' => null, 'city' => null, 'state' => null, 'zip' => null];

        if (str_starts_with($requestURI, '/users')) {
            $original = User::find($entity_id) ?? $original;
        } elseif (str_starts_with($requestURI, '/locations')) {
            $original = Location::find($entity_id) ?? $original;
        } else {
            throw new \Exception("Unknown Request Type.");
        }

        $validate = (new Usps(['username' => env('USPS_USERNAME')]))->validate([
            'Address' => $request->get('address1', $original['address1']),
            'City' => $request->get('city', $original['city']),
            'State' => $request->get('state', $original['state']),
            'Zip' => (string) $request->get('zip', $original['zip']),
        ]);

        if (array_key_exists('error', $validate) || $validate['address']['Zip5'] <> (string)$value) {
            $fail("Invalid Zip Code");
        }
    }
}
