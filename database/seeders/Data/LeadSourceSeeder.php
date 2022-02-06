<?php

namespace Database\Seeders\Data;

use App\Models\Clients\Client;
use App\Models\Endusers\LeadSource;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

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
                LeadSource::create([
                    'client_id' => $client->id,
                    'name' => $readable_source,
                    'source' => $lead_source,
                    'ui' => 1
                ]);

                VarDumper::dump("Adding lead source {$readable_source}");
            }
        }
    }

}
