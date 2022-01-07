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
            'source-0',
            'source-1',
            'source-2',
            'source-3',
            'source-4',
            'source-5',
            'source-6',
        ];

        $clients = Client::all();
        foreach ($clients as $client) {
            foreach ($lead_sources as $lead_source) {
                LeadSource::create(['client_id' => $client->id, 'name' => $lead_source]);
                VarDumper::dump("Adding lead source {$lead_source}");
            }
        }
    }

}
