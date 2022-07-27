<?php

namespace App\Imports;

use App\Domain\Departments\Department;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\User;
use App\Models\Clients\Location;
use App\Models\Position;
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
        $roles = Role::whereScope($this->client_id)->whereTitle('Employee')->first();

        foreach ($rows as $row) {
            $arrayRow = $row->toArray();

            if (User::whereEmail($row['email'])->first() !== null) {
                continue;
            }

            $team_ids = [];
            if (array_key_exists('location_no', $arrayRow)) {
                $location = Location::whereLocationNo($row['location_no'])->first();
                if (! $location) {
                    continue;
                }
                $team_ids[] = $location->default_team_id;
            }


            $department_ids = [];
            if (array_key_exists('departments', $arrayRow)) {
                $departments = explode(",", $row['departments']);
                foreach ($departments as $department) {
                    $dept = department::whereName($department)->first();
                    if (! $dept) {
                        continue;
                    }
                    $department_ids[] = $dept->id;
                }
            }


            $position_ids = [];
            if (array_key_exists('positions', $arrayRow)) {
                $positions = explode(",", $row['positions']);
                foreach ($positions as $position) {
                    $pos = position::whereName($position)->first();
                    if (! $pos) {
                        continue;
                    }
                    foreach ($department_ids as $id) {
                        $potential_dept = Department::whereId($id)->with('positions')->first();
                    }
                    $position_ids[] = $pos->id;
                }
            }

            CreateUser::run([
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
                'positions' => is_null($position_ids) ? null : $position_ids,
                'departments' => is_null($department_ids) ? null : $department_ids,
                'client_id' => $this->client_id,
            ]);
        }
    }
}
