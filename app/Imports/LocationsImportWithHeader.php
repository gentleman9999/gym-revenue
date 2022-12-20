<?php

namespace App\Imports;

use App\Domain\Locations\Actions\CreateLocation;
use App\Enums\LocationTypeEnum;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LocationsImportWithHeader implements ToCollection, WithHeadingRow
{
    protected string $client_id;

    public function __construct(string $client)
    {
        $this->client_id = $client;
    }

    public function collection(Collection|\Illuminate\Support\Collection $rows)
    {
        foreach ($rows as $row) {
            CreateLocation::run([
                'client_id' => $this->client_id,
                'name' => $row['name'],
                'location_no' => $row['location_no'],
                'city' => $row['city'],
                'state' => $row['state'],
                'zip' => $row['zip'],
                'address1' => $row['address1'],
                'phone' => $row['phone'],
                'open_date' => $row['open_date'] ?? null,
                'close_date' => $row['close_date'] ?? null,
                'poc_first' => $row['poc_first'] ?? null,
                'poc_last' => $row['poc_last'] ?? null,
                'poc_phone' => $row['poc_phone'] ?? null,
                'shouldCreateTeam' => true,
                'location_type' => LocationTypeEnum::STORE,
                'capacity' => $row['capacity'],
            ]);
        }
    }
}
