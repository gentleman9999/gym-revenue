<?php

declare(strict_types=1);

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
        $amount_of_leads = 20;
        if (env('QUICK_SEED')) {
            $amount_of_leads = 3;
        }

        if (env('RAPID_SEED') === true) {
            $amount_of_leads = 1;
        }
        // Get all the Clients
        $clients = Client::with('locations')->whereActive(1)->get();

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump($client->name);
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    VarDumper::dump('Generating End Users for '.$client->name.'!');
                    foreach ($client->locations as $idx => $location) {
                        // For each location, MAKE 25 users, don't create
                        foreach ([
                                UserTypesEnum::LEAD,
                                UserTypesEnum::CUSTOMER,
                                UserTypesEnum::MEMBER,
                            ] as $user_type
                        ) {
                            $end_users = User::factory()->count($amount_of_leads)->make();

                            foreach ($end_users as $end_user) {
                                $end_user_data = $end_user->toArray();
                                if (User::whereClientId($client->id)->whereEmail($end_user_data['email'])->exists()) {
                                    continue;
                                }
                                $end_user_data['client_id'] = $client->id;
                                $end_user_data['home_location_id'] = $location->gymrevenue_id;
                                $end_user_data['user_type'] = $user_type;
                                $end_user_data['password'] = 'Hello123!';

                                if ($user_type == 'lead') {
                                    $end_user_data['opportunity'] = rand(0, 3);
                                    $end_user_data['opportunity'] = $end_user_data['opportunity'] == 0 ?
                                        null : $end_user_data['opportunity'];
                                }

                                CreateUser::run($end_user_data);
                            }
                        }
                    }
                }
            }
        }
    }
}
