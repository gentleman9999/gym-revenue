<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
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
        $clients = Client::whereActive(1)->get();

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump($client->name);
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        // For each location, MAKE 25 users, don't create
                        foreach ([
                                UserTypesEnum::LEAD,
                                UserTypesEnum::CUSTOMER,
                                UserTypesEnum::MEMBER,
                            ] as $user_type
                        ) {
                            $prospects = User::factory()->count($amountOfLeads)->make();

                            VarDumper::dump('Generating End Users for '.$client->name.'!');
                            foreach ($prospects as $prospect) {
                                $prospect_data = $prospect->toArray();
                                $prospect_data['client_id'] = $client->id;
                                $prospect_data['home_location_id'] = $location->gymrevenue_id;
                                $prospect_data['user_type'] = $user_type;
                                $prospect_data['password'] = 'Hello123!';

                                if ($user_type == 'lead') {
                                    $prospect_data['opportunity'] = rand(0, 3);
                                    $prospect_data['opportunity'] = $prospect_data['opportunity'] == 0 ?
                                        null : $prospect_data['opportunity'];
                                }

                                CreateUser::run($prospect_data);
                            }
                        }
                    }
                }
            }
        }
    }
}
