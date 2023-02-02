<?php

namespace Database\Seeders\Comm;

use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\CallScriptTemplates\Actions\CreateCallScriptTemplate;
use Illuminate\Database\Seeder;

class CallScriptTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_markup = "Hi this is a call script for a client at GymRevenue!  -GymmieBot";
        // For each client
        foreach (Client::whereActive(1)->get() as $client) {
            echo("Default Call Script template for {$client->name}\n");
            // Create an email template record
            CreateCallScriptTemplate::run([
                'name'      => $client->name . "'s First Call Script Template (;",
                'client_id' => $client->id,
                'active'    => 1,
                'script'    => $default_markup,
                'json'      => '',
            ]);
        }
    }
}
