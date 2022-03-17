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

    public function __construct(string $client) {
        $this->client_id = $client;
    }

    public function collection(Collection|\Illuminate\Support\Collection $rows)
    {
        foreach ($rows as $row)
        {
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
            ]);

            $row = $row->toArray();
            if(array_key_exists('phone', $row))
            {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'phone',
                    'value' => $row['phone']
                ]);
            }
            if(array_key_exists('open_date', $row))
            {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'open_date',
                    'value' => $row['open_date']
                ]);
            }
            if(array_key_exists('close_date', $row))
            {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'close_date',
                    'value' => $row['close_date']
                ]);
            }
            if(array_key_exists('poc_first', $row))
            {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'poc_first',
                    'value' => $row['poc_first']
                ]);
            }
            if(array_key_exists('poc_last', $row))
            {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'poc_last',
                    'value' => $row['poc_last']
                ]);
            }
            if(array_key_exists('poc_phone', $row))
            {
                LocationDetails::create([
                    'location_id' => $test->id,
                    'client' => $this->client_id,
                    'field' => 'poc_last',
                    'value' => $row['poc_last']
                ]);
            }
        }
    }

}
