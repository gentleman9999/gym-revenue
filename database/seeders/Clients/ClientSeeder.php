<?php

namespace Database\Seeders\Clients;

use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Endusers\Service;
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

        $services = Service::all();

        foreach ($clients as $name => $active)
        {
            $client = Client::firstOrCreate([
                'name' => $name,
                'active' => $active
            ]);

            foreach($services as $service){
                ClientDetail::create([
                    'client_id' => $client->id,
                    'detail' => 'service_id',
                    'value' => $service->id
                ]);
            }

        }
    }
}
