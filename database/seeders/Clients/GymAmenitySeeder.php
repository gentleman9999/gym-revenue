<?php

namespace Database\Seeders\Clients;

use App\Domain\Clients\Projections\Client;
use App\Domain\GymAmenities\Actions\CreateGymAmenity;
use App\Services\Process;
use Illuminate\Database\Seeder;

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

        $process = Process::allocate(5);
        $clients = Client::with('locations')->get();

        foreach ($clients as $client) {
            echo("Creating Gym Amenities for client {$client['name']}\n");
            foreach ($client->locations as $location) {
                foreach ($gym_amenities as $gym_amenity) {
                    $gym_amenity['client_id'] = $client->id;
                    $gym_amenity['location_id'] = $location->id;
                    $process->queue([CreateGymAmenity::class, 'run'], $gym_amenity);
                }
            }
        }

        $process->run();
    }
}
