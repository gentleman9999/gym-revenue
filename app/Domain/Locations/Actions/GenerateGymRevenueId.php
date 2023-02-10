<?php

declare(strict_types=1);

namespace App\Domain\Locations\Actions;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateGymRevenueId
{
    use AsAction;

    public function handle(string $client_id): ?string
    {
        $prefix = Client::findOrFail($client_id)->prefix;

        if ($prefix !== null) {
            $iterations = Location::all()->pluck('gymrevenue_id');
            $value      = 001;

            if ((count($iterations) > 0) && Str::contains($iterations[count($iterations) - 1], $prefix[0])) {
                $value = (int) str_replace($prefix[0], "", $iterations[count($iterations) - 1]) + 1;
            }

            return $prefix[0] . '' . sprintf('%03d', $value);
        } else {
            // @todo - throw a custom Client Prefix now found Exception
        }

        return null;
    }
}
