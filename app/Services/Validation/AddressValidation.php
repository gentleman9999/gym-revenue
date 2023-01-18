<?php

declare(strict_types=1);

namespace App\Services\Validation;

use App\Domain\Locations\Projections\Location;
use App\Domain\Users\Models\User;
use Johnpaulmedina\Usps\Usps;

class AddressValidation
{
    public static function validate(string $request_uri, array $payload): array
    {
        /** Subtracting the length of the UUID */
        $entity_id = substr($request_uri, strlen($request_uri) - 36);
        $original = self::getOriginalEntityIfExists($request_uri, $entity_id);
        $payload = self::overwriteNullPayloadValues($payload, $original);
        $validator = self::validateAddressData($payload);

        return self::generateResult($validator, $payload);
    }

    protected static function getOriginalEntityIfExists(string $request_uri, string $entity_id): array
    {
        $entity = null;
        $original = ['address1' => '', 'city' => '', 'state' => '', 'zip' => ''];

        if (preg_match('/^(\/data|\/(customer|lead|member|user)s)/', $request_uri) > 0) {
            $entity = User::find($entity_id);
        } elseif (str_starts_with($request_uri, '/locations')) {
            $entity = Location::find($entity_id);
        } else {
            throw new \Exception("Unknown Request Type.");
        }

        return is_null($entity) ? $original : $entity->toArray();
    }

    protected static function overwriteNullPayloadValues(array $payload, array $original): array
    {
        $payload['address1'] = $payload['address1'] ?? $original['address1'];
        $payload['city'] = $payload['city'] ?? $original['city'];
        $payload['state'] = $payload['state'] ?? $original['state'];
        $payload['zip'] = (string) $payload['zip'] ?? $original['zip'];

        return $payload;
    }

    protected static function validateAddressData(array $payload): array
    {
        return (new Usps(['username' => env('USPS_USERNAME')]))->validate([
            'Address' => $payload['address1'],
            'City' => $payload['city'],
            'State' => $payload['state'],
            'Zip' => (string) $payload['zip'],
        ]);
    }

    protected static function generateResult(array $validator, array $payload): array
    {
        $result = ['errors' => [], 'validated_data' => []];
        $key_col_map = [
            'Address2' => ['col_name' => 'address1', 'error_field_name' => 'address'],
            'City' => ['col_name' => 'city', 'error_field_name' => 'city name'],
            'State' => ['col_name' => 'state', 'error_field_name' => 'state code'],
            'Zip5' => ['col_name' => 'zip', 'error_field_name' => 'zip code'],
        ];

        if (array_key_exists('error', $validator)) {
            $validator['error'] = strtolower($validator['error']);
            if (str_contains($validator['error'], 'address')) {
                $result['errors']['address1'] = 'Invalid address';
            }

            if (str_contains($validator['error'], 'zip')) {
                $result['errors']['zip'] = 'Invalid zip code';
            }
        } else {
            foreach ($key_col_map as $key => $map) {
                if (strtoupper($payload[$map['col_name']]) != strtoupper((string) $validator['address'][$key])) {
                    $result['errors'][$map['col_name']] = "Invalid {$map['error_field_name']}";
                }

                $result['validated_data'][$map['col_name']] = $validator['address'][$key];
            }
        }

        return $result;
    }
}
