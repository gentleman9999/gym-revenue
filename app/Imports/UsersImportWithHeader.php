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
        $roles = Role::whereScope($this->client_id)->whereTitle('Employee')->first();
        $team_ids = $client->teams()->pluck('id');

        foreach ($rows as $row) {
            $arrayRow = $row->toArray();

            $location = CreateUser::run([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'password' => 'Hello123!',
                'team_ids' => $team_ids,
                'role_id' => $roles->id,
                'home_club' => array_key_exists('home_club', $arrayRow) ? $row['phone'] : null,
                'phone' => array_key_exists('phone', $arrayRow) ? $row['phone'] : null,
                'address1' => array_key_exists('address1', $arrayRow) ? $row['address1'] : null,
                'address2' => array_key_exists('address2', $arrayRow) ? $row['address2'] : null,
                'city' => array_key_exists('city', $arrayRow) ? $row['city'] : null,
                'state' => array_key_exists('state', $arrayRow) ? $row['state'] : null,
                'zip' => array_key_exists('zip', $arrayRow) ? $row['zip'] : null,
                'client_id' => $this->client_id,
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
