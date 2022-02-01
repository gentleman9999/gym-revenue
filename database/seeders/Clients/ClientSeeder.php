<?php

namespace Database\Seeders\Clients;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
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

        $services = [['feature_name' => 'Free Trial/Guest Pass Memberships', 'slug' => 'free-trial']];


        foreach ($clients as $name => $active) {
            $client = Client::firstOrCreate([
                'name' => $name,
                'active' => $active
            ]);

            foreach ($services as $service) {
                ClientAggregate::retrieve($client->id)->addClientService($service['feature_name'], $service['slug'], true)->persist();
            }

        }
    }
}
