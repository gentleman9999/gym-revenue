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
        $request_uri = request()->getRequestUri();
        $entity_id = substr($request_uri, strlen($request_uri) - 36); // Subtracting the length of the UUID

        if (preg_match('/^(\/data\/(customer|lead|member)|user)s/', $request_uri) > 0) {
            $original = (new User())->whereId($entity_id)->first();
        } elseif (str_starts_with($request_uri, '/locations')) {
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
