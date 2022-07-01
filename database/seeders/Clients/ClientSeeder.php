<?php

namespace Database\Seeders\Clients;

use App\Actions\Jetstream\CreateTeam;
use App\Aggregates\Clients\ClientAggregate;
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
            'iFit' => 1,
            'TruFit Athletic Clubs' => 1,
            'Stencils' => 1,
            'The Z' => 1,
            'Sci-Fi Purple Gyms' => 1,
            'FitnessTruth' => 1,
        ];

        $services = [
            ['feature_name' => 'Free Trial/Guest Pass Memberships', 'slug' => 'FREE_TRIAL'],
            ['feature_name' => 'Mass Communications', 'slug' => 'MASS_COMMS'],
            ['feature_name' => 'Calendar', 'slug' => 'CALENDAR'],
            ['feature_name' => 'Leads', 'slug' => 'LEADS'],
            ['feature_name' => 'Members', 'slug' => 'MEMBERS'],
        ];


        foreach ($clients as $name => $active) {
            $client = Client::firstOrCreate([
                'name' => $name,
                'active' => $active,
            ]);

            $default_team_name = $name . ' Home Office';
            preg_match_all('/(?<=\s|^)[a-z]/i', $default_team_name, $matches);
            $prefix = strtoupper(implode('', $matches[0]));
            CreateTeam::run(['name' => $default_team_name, 'default_team' => true, 'client_id' => $client->id]);
            $aggy = ClientAggregate::retrieve($client->id)
//                ->createDefaultTeam($default_team_name)
                ->createTeamPrefix((strlen($prefix) > 3) ? substr($prefix, 0, 3) : $prefix)
                ->createAudience("{$client->name} Prospects", 'prospects', /*env('MAIL_FROM_ADDRESS'),*/ 'auto')
                ->createAudience("{$client->name} Conversions", 'conversions', /*env('MAIL_FROM_ADDRESS'),*/ 'auto')
                ->createGatewayIntegration('sms', 'twilio', 'default_cnb', 'auto')
                ->createGatewayIntegration('email', 'mailgun', 'default_cnb', 'auto')
                // @todo - add more onboarding shit here.
            ;
            $aggy->persist();

            foreach ($services as $service) {
                ClientAggregate::retrieve($client->id)->addClientService($service['feature_name'], $service['slug'], true)->persist();
            }
        }
    }
}
