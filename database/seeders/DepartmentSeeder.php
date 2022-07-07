<?php

namespace Database\Seeders;

use App\Domain\Clients\Models\Client;
use App\Models\Department;
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

        $items = [
            'Operations',
            'Sales',
            'Marketing',
            'Fitness',
            'Finance',
            'IT',
            ];

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                foreach ($items as $i) {
                    Department::create([
                        'id' => Uuid::new(),
                        'client_id' => $client->id,
                        'name' => $i,
                    ]);
                }
            }
        }
    }
}
