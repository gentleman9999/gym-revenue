<?php

namespace Database\Seeders;

use App\Domain\Clients\Models\Client;
use App\Models\Department;
use App\Models\Position;
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
            $pos = Position::whereClientId($client->id)->whereName('Front Desk')->first();
            $dept = Department::whereClientId($client->id)->whereName('Sales')->first();

            //$dept->positions()->sync([$pos->id]);

            $pos->departments()->sync([$dept->id]);
        }
    }
}
