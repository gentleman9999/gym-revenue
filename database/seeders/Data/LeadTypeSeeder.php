<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\LeadTypes\Actions\CreateLeadType;
use App\Services\Process;
use Illuminate\Database\Seeder;

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
            'mailing_list',
        ];

        $clients = Client::all();
        $process = Process::allocate(5);
        foreach ($clients as $client) {
            foreach ($lead_types as $lead_type) {
                echo("Adding lead type {$lead_type}\n");
                $process->queue([CreateLeadType::class, 'run'], ['client_id' => $client->id, 'name' => $lead_type]);
            }
        }
        $process->run();
    }
}
