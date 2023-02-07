<?php

namespace Database\Seeders\Comm;

use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\SmsTemplates\Actions\CreateSmsTemplate;
use Illuminate\Database\Seeder;

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
        echo('Default sms template for Cape & Bay' . PHP_EOL);
        CreateSmsTemplate::run([
            'name' => "Baby's First SMS Template (;",
            'active' => 1,
            'created_by_user_id' => 'auto',
            'markup' => $default_markup,
        ]);

        // For each client
        foreach (Client::whereActive(1)->get() as $client) {
            echo("Default SMS template for {$client->name}\n");
            // Create an email template record
            CreateSmsTemplate::run([
                'name' => "{$client->name}'s First SMS Template (;",
                'client_id' => $client->id,
                'active' => 1,
                'created_by_user_id' => 'auto',
                'markup' => $default_markup,
            ]);
        }
    }
}
