<?php

namespace Database\Seeders\Comm;

use App\Domain\Clients\Models\Client;
use App\Models\Comms\SmsTemplates;
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
        $default_markup = "Hello, %name%! Welcome to GymRevenue!  -GymmieBot</body></html>";
        // For Cape & Bay
        VarDumper::dump('Default sms template for Cape & Bay');
        $cnb_record = SmsTemplates::firstOrCreate([
            'name' => "Baby's First SMS Template (;",
            'active' => 1,
            'created_by_user_id' => 'auto',
        ]);
        $cnb_record->markup = $default_markup;
        $cnb_record->save();

        $clients = Client::whereActive(1)->get();
        // For each client
        foreach ($clients as $client) {
            VarDumper::dump('Default SMS template for '.$client->name);
            // Create an email template record
            $record = SmsTemplates::firstOrCreate([
                'name' => $client->name."'s First SMS Template (;",
                'client_id' => $client->id,
                'active' => 1,
                'created_by_user_id' => 'auto',
            ]);
            // Mark sure markup is stupid simple with %name% token and base64 encoded
            //$record->markup = base64_encode($default_markup);
            $record->markup = $default_markup;
            $record->save();
        }
    }
}
