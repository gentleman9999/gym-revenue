<?php

namespace Database\Seeders;

use App\Domain\Clients\Projections\Client;
use App\Domain\Departments\Department;
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
            $sales_dept = Department::whereClientId($client->id)->whereName('Sales')->first();
            $sales_pos = Position::whereClientId($client->id)->whereName('Fitness Sales Rep')->first();
            $sales_pos2 = Position::whereClientId($client->id)->whereName('Sales Manager')->first();
            $sales_pos3 = Position::whereClientId($client->id)->whereName('Sales Director')->first();
            $sales_dept->positions()->sync([$sales_pos->id, $sales_pos2->id, $sales_pos3->id]);

            $operations = Department::whereClientId($client->id)->whereName('Operations')->first();
            $pos = Position::whereClientId($client->id)->whereName('Childcare')->first();
            $pos2 = Position::whereClientId($client->id)->whereName('Front Desk')->first();
            $pos3 = Position::whereClientId($client->id)->whereName('Maintenance')->first();
            $operations->positions()->sync([$pos->id, $pos2->id, $pos3->id]);

            $operations = Department::whereClientId($client->id)->whereName('Fitness')->first();
            $pos = Position::whereClientId($client->id)->whereName('Fitness Sales Rep')->first();
            $pos2 = Position::whereClientId($client->id)->whereName('Fitness Director')->first();
            $pos3 = Position::whereClientId($client->id)->whereName('Fitness Manager')->first();
            $operations->positions()->sync([$pos->id, $pos2->id, $pos3->id]);

            //$pos->departments()->sync([$dept->id]);
        }
    }
}
