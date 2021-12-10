<?php

namespace Database\Seeders\Comm;

use App\Models\Clients\Client;
use App\Models\Comms\EmailTemplates;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_markup = "<html><body>Hello, %name%!<br/> Have a great day!<br /><br /><br /><span>Sincerely,</span><br/><br/><br/><br/>GymmieBot</body></html>";
        // For Cape & Bay
        VarDumper::dump('Default email template for Cape & Bay');
        $cnb_record = EmailTemplates::firstOrCreate([
            'name' => "Baby's First Email Template (;",
            'active' => 1,
            'created_by_user_id' => 'auto'
        ]);
        $cnb_record->markup = base64_encode($default_markup);
        $cnb_record->save();

        $clients = Client::whereActive(1)->get();
        // For each client
        foreach ($clients as $client)
        {
            VarDumper::dump('Default email template for '.$client->name);
            // Create an email template record
            $record = EmailTemplates::firstOrCreate([
                'name' => $client->name."'s First Email Template (;",
                'client_id' => $client->id,
                'active' => 1,
                'created_by_user_id' => 'auto'
            ]);
            // Mark sure markup is stupid simple with %name% token and base64 encoded
            //$record->markup = base64_encode($default_markup);
            $record->markup = $default_markup;
            $record->save();
        }

    }
}
