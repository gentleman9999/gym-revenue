<?php

namespace Database\Seeders\Comm;

use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\SmsTemplates\Actions\CreateSmsTemplate;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class SMSTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_markup = "Hello, %name%! Welcome to GymRevenue!  -GymmieBot";
        // For Cape & Bay
        VarDumper::dump('Default sms template for Cape & Bay');
        CreateSmsTemplate::run([
            'name' => "Baby's First SMS Template (;",
            'active' => 1,
            'created_by_user_id' => 'auto',
            'markup' => $default_markup,
        ]);

        $clients = Client::whereActive(1)->get();
        // For each client
        foreach ($clients as $client) {
            VarDumper::dump('Default SMS template for '.$client->name);
            // Create an email template record
            CreateSmsTemplate::run([
                'name' => $client->name."'s First SMS Template (;",
                'client_id' => $client->id,
                'active' => 1,
                'created_by_user_id' => 'auto',
                'markup' => $default_markup,
            ]);
        }
    }
}
