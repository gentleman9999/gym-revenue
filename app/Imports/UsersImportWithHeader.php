<?php

namespace App\Imports;

use App\Actions\Fortify\CreateUser;
use App\Models\Clients\Client;
use App\Models\Clients\LocationDetails;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Silber\Bouncer\Database\Role;

class UsersImportWithHeader implements ToCollection, WithHeadingRow
{
    protected string $client_id;

    public function __construct(string $client)
    {
        $this->client_id = $client;
    }

    public function collection(Collection|\Illuminate\Support\Collection $rows)
    {
        $client = Client::with('teams')->find($this->client_id);
        $roles = Role::whereScope($client->id)->get();
        $team_ids = $client->teams()->pluck('value');
        foreach ($rows as $row) {
            $arrayRow = $row->toArray();

            $location = CreateUser::run([
                'first_name' => $row[0],
                'last_name' => $row[1],
                'email' => $row[2],
                'password' => 'Hello123!',
                'team_ids' => $team_ids,
                'role' => 4,
                'home_club' => 0,
                'is_manager' => 0,
                //'phone' => array_key_exists('phone', $arrayRow) ? $row['phone'] : null,
                //'open_date' => array_key_exists('open_date', $arrayRow) ? $row['open_date'] : null,
                //'close_date' => array_key_exists('close_date', $arrayRow) ? $row['close_date'] : null,
            ]);

            if (array_key_exists('poc_phone', $arrayRow)) {
                LocationDetails::create([
                    'location_id' => $location->id,
                    'client' => $this->client_id,
                    'field' => 'poc_phone',
                    'value' => $row['poc_phone'],
                ]);
            }
        }
    }
}
