<?php

namespace Database\Seeders\Clients;

use App\Models\Clients\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = [
            'The Kalamazoo' => 1,
            'Bodies By Brett' => 1,
            'iFit' => 1,
            'TruFit Athletic Clubs' => 0,
            'Stencils' => 1,
            'The Z' => 1,
            'Sci-Fi Purple Gyms' => 1,
            'FitnessTruth' => 1,
        ];

        foreach ($clients as $name => $active)
        {
            Client::firstOrCreate([
                'name' => $name,
                'active' => $active
            ]);
        }
    }
}
