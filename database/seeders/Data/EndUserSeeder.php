<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Actions\CreateEndUser;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class EndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $amountOfLeads = 20;
        if (env('QUICK_SEED')) {
            $amountOfLeads = 2;
        }

        if (env('RAPID_SEED') === true) {
            $amountOfLeads = 1;
        }
        VarDumper::dump('Getting Clients');
        // Get all the Clients
        $clients = Client::whereActive(1)
            ->get();

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump($client->name);
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        // For each location, MAKE 25 users, don't create
                        $prospects = EndUser::factory()->count($amountOfLeads)
                            // over ride the client id and gr id from the factory
                            ->client_id($client->id)
                            ->gr_location_id($location->gymrevenue_id ?? '')
                            ->make();

                        VarDumper::dump('Generating End Users for '.$client->name.'!');
                        foreach ($prospects as $prospect) {
                            $prospect_data = $prospect->toArray();
                            $prospect_data['client_id'] = $prospect['client_id'];
                            $prospect_data['gr_location_id'] = $location->gymrevenue_id;

                            CreateEndUser::run($prospect_data);
                        }
                    }
                }
            }
        }
    }
}
