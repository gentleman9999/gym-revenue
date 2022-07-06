<?php

namespace Database\Seeders;

use App\Models\Clients\Client;
use App\Models\Department;
use Illuminate\Database\Seeder;

class PositionDepartmentSync extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::whereActive(1)->get();

        foreach ($clients as $client) {
            $dept = Department::whereClientId($client->id)
                ->whereName('Sales')
                ->get();
            $dept->sync(['Front Desk']);
        }
    }
}
