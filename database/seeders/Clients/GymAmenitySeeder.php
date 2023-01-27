<?php

namespace Database\Seeders\Clients;

use App\Domain\Clients\Projections\Client;
use App\Domain\GymAmenities\Actions\CreateGymAmenity;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class GymAmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gym_amenities = [
            [
                'name' => 'Massages',
                'capacity' => 20,
                'working_hour' => 5,
            ],
            [
                'name' => 'Saunas',
                'capacity' => 10,
                'working_hour' => 8,
            ],
        ];

        $clients = Client::with('locations')->get();

        foreach ($clients as $client) {
            VarDumper::dump("Creating Gym Amenities for client {$client['name']}");
            foreach ($client->locations as $location) {
                foreach ($gym_amenities as $gym_amenity) {
                    $gym_amenity['client_id'] = $client->id;
                    $gym_amenity['location_id'] = $location->id;
                    CreateGymAmenity::run($gym_amenity);
                }
            }
        }
    }
}
