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
        $requestURI = request()->getRequestUri();
        $entity_id = substr($requestURI, strpos($requestURI, '/', 1) + 1);

        if (str_starts_with($requestURI, '/users')) {
            $original = (new User())->whereId($entity_id)->first();
        } elseif (str_starts_with($requestURI, '/locations')) {
            $original = (new Location())->whereId($entity_id)->first();
        } else {
            throw new \Exception("Unknown Request Type.");
        }

        $request = request();

        $validate = (new Usps(['username' => env('USPS_USERNAME')]))->validate(
            [
            'Address' => $request->get('address1', $original['address1']),
            'City' => $request->get('city', $original['city']),
            'State' => $request->get('state', $original['state']),
            'Zip' => (string)$request->get('zip', $original['zip']),
        ]
        );

        if (array_key_exists('error', $validate) || $validate['address']['Zip5'] <> (string)$value) {
            $fail("Invalid Zip Code");
        }
    }
}
