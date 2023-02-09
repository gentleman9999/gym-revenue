<?php

namespace Database\Seeders;

use App\Domain\Clients\Projections\Client;
use App\Domain\Departments\Department;
use App\Support\Uuid;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::whereActive(1)->get();
        if (count($clients) === 0) {
            return;
        }

        $items = [
            'Operations',
            'Sales',
            'Marketing',
            'Fitness',
            'Finance',
            'IT',
        ];

        $data = [];
        foreach ($clients as $client) {
            foreach ($items as $i) {
                $data[] = [
                    'id' => Uuid::new(),
                    'client_id' => $client->id,
                    'name' => $i,
                ];
            }
        }

        Department::insert($data);
    }
}
