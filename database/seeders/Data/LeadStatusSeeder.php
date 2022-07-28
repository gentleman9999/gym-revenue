<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\LeadStatuses\Actions\CreateLeadStatus;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lead_types = [
            'New',
            'Not Interested',
            'No Show',
            'Missed',
        ];

        $clients = Client::all();
        foreach ($clients as $client) {
            foreach ($lead_types as $idx => $lead_type) {
                VarDumper::dump("Adding lead status {$lead_type}");
                CreateLeadStatus::run([
                    'client_id' => $client->id,
                    'status' => $lead_type,
                    'order' => $idx + 1,
                    'active' => 1,
                ]);
            }
        }
    }
}
