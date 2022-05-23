<?php

namespace App\Imports;

use App\Actions\Clients\Locations\GenerateGymRevenueId;
use App\Models\Clients\Location;
use App\Models\Clients\LocationDetails;
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
            $arrayRow = $row->toArray();
            $gr_id = GenerateGymRevenueId::run($this->client_id);
            $test = Location::create([
                'client_id' => $this->client_id,
                'gymrevenue_id' => $gr_id,
                'name' => $row['name'],
                'location_no' => $row['location_no'],
                'city' => $row['city'],
                'state' => $row['state'],
                'zip' => $row['zip'],
                'address1' => $row['address1'],
                'phone' => array_key_exists('phone', $arrayRow) ? $row['phone'] : null,
                'poc_phone' => array_key_exists('poc_phone', $arrayRow) ? $row['poc_phone'] : null,
                'open_date' => array_key_exists('open_date', $arrayRow) ? $row['open_date'] : null,
                'close_date' => array_key_exists('close_date', $arrayRow) ? $row['close_date'] : null,
            ]);

            if (array_key_exists('poc_first', $arrayRow)) {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'poc_first',
                    'value' => $row['poc_first'],
                ]);
            }
            if (array_key_exists('poc_last', $arrayRow)) {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'poc_last',
                    'value' => $row['poc_last'],
                ]);
            }
        }
    }
}
