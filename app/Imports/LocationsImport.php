<?php

namespace App\Imports;
use App\Actions\Clients\Locations\GenerateGymRevenueId;
use App\Models\Clients\Location;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class LocationsImport implements ToCollection
{

    protected string $client_id;

    public function __construct(string $client) {
        $this->client_id = $client;
    }

    public function collection(Collection|\Illuminate\Support\Collection $rows)
    {
        foreach ($rows as $row)
        {
            $gr_id = GenerateGymRevenueId::run($this->client_id);
            Location::create([
                'client_id' => $this->client_id,
                'gymrevenue_id' => $gr_id,
                'name' => $row[0],
                'location_no' => $row[1],
                'city' => $row[2],
                'state' => $row[3],
                'zip' => $row[4],
                'address1' => $row[5],
            ]);
        }
    }

}
