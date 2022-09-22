<?php

namespace Database\Seeders\Comm;

use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\CallScriptTemplates\Actions\CreateCallScriptTemplate;
use App\Domain\Templates\CallScriptTemplates\Actions\UpdateCallScriptTemplate;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

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

//        // For Cape & Bay
//        VarDumper::dump('Default Call script template for Cape & Bay');
//        $template = CreateCallScriptTemplate::run([
//            'name' => "Baby's First Call Script Template (;",
//            'active' => 1,
//            'markup' => $default_markup,
//            'json' => ''
//        ]);
//
//        $template->active = 1;
//
//        UpdateEmailTemplate::run($template->toArray());
//
        $clients = Client::whereActive(1)->get();
        // For each client
        foreach ($clients as $client) {
            VarDumper::dump('Default Call Script template for '.$client->name);
            // Create an email template record
            $template = CreateCallScriptTemplate::run([
                'name' => $client->name."'s First Call Script Template (;",
                'client_id' => $client->id,
                'active' => 1,
                'script' => $default_markup,
                'json' => '',
            ]);

            //$template->active = 1;

            //UpdateCallScriptTemplate::run($template, $template->toArray());
        }
    }
}
