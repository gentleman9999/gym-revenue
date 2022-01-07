<?php

namespace Database\Seeders\Data;

use App\Models\Clients\Client;
use App\Models\Endusers\LeadType;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class LeadTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lead_types = [
            'manual_create',
            'free_trial',
            'grand_opening',
            'streaming_preview',
            'personal_training',
            'app_referral',
            'facebook',
            'snapchat',
            'contact_us',
            'mailing_list'
        ];

        $clients = Client::all();
        foreach ($clients as $client) {
            foreach ($lead_types as $lead_type) {
                LeadType::create(['client_id' => $client->id, 'name' => $lead_type]);
                VarDumper::dump("Adding lead type {$lead_type}");
            }
        }
    }

}
