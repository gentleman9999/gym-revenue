<?php

namespace Database\Seeders\Clients;

use App\Actions\Clients\CreateClient;
use App\Aggregates\Clients\ClientAggregate;
use App\Enums\ClientServiceEnum;
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
            'TruFit Athletic Clubs' => 1,
            'Stencils' => 1,
            'The Z' => 1,
            'Sci-Fi Purple Gyms' => 1,
            'FitnessTruth' => 1,
        ];

//        $services = [['feature_name' => 'Free Trial/Guest Pass Memberships', 'slug' => 'free-trial']];


        foreach ($clients as $name => $active) {
            $client = CreateClient::run(
                [
                    'name' => $name,
                    'active' => $active,
                    'services' => ClientServiceEnum::cases(),
                ]
            );

            $aggy = ClientAggregate::retrieve($client->id)
//                ->createAudience("{$client->name} Prospects", 'prospects', /*env('MAIL_FROM_ADDRESS'),*/ 'auto')
//                ->createAudience("{$client->name} Conversions", 'conversions', /*env('MAIL_FROM_ADDRESS'),*/ 'auto')
                ->createGatewayIntegration('sms', 'twilio', 'default_cnb', 'auto')
                ->createGatewayIntegration('email', 'mailgun', 'default_cnb', 'auto')
            ;
            $aggy->persist();
//
//            foreach ($services as $service) {
//                ClientAggregate::retrieve($client->id)->addClientService($service['feature_name'], $service['slug'], true)->persist();
//            }
        }
    }
}
