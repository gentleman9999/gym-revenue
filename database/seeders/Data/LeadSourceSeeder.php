<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\LeadSources\Actions\CreateLeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lead_sources = [
            'walk-in' => 'Walk In',
            'buddy-referral' => 'Buddy Referral',
            'member-guest' => 'Member Guest Pass',
            'facebook' => 'Meta/Facebook/Instagram',
            'guest-pass' => 'Guest Pass',
            'custom' => 'Custom',
        ];

        $clients = Client::all();
        foreach ($clients as $client) {
            foreach ($lead_sources as $lead_source => $readable_source) {
                CreateLeadSource::run([
                    'name' => $readable_source,
                    'source' => $lead_source,
                    'ui' => 1,
                    'client_id' => $client->id,
                ]);


                echo("Adding lead source {$readable_source}\n");
            }
        }
    }
}
