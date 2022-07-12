<?php

namespace App\Actions\Clients\Locations;

use App\Domain\Clients\Models\Client;
use App\Models\Clients\Location;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateGymRevenueId
{
    use AsAction;

    public function handle(string $client_id)
    {
        $results = false;

        $prefix = Client::findOrFail($client_id)->prefix;

        if (! is_null($prefix)) {
            $iterations = Location::whereClientId($client_id)->pluck('gymrevenue_id');
            $value = 001;

            if ((count($iterations) > 0) && Str::contains($iterations[count($iterations) - 1], $prefix[0])) {
                $value = (int) str_replace($prefix[0], "", $iterations[count($iterations) - 1]) + 1;
            }

            $results = $prefix[0].''.sprintf('%03d', $value);
        } else {
            // @todo - throw a custom Client Prefix now found Exception
        }

        return $results;
    }
}
