<?php

namespace Database\Seeders\Comm;

use App\Actions\Clients\Activity\Comms\CreateEmailTemplate;
use App\Actions\Clients\Activity\Comms\UpdateEmailTemplate;
use App\Models\Clients\Client;
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
//        // For Cape & Bay
//        VarDumper::dump('Default email template for Cape & Bay');
//        $template = CreateEmailTemplate::run([
//            'name' => "Baby's First Email Template (;",
//            'active' => 1,
//            'markup' => $default_markup,
//            'json' => ''//TODO:
//        ]);
//
//        $template->active = 1;
//
//        UpdateEmailTemplate::run($template->toArray());
//
        $clients = Client::whereActive(1)->get();
        // For each client
        foreach ($clients as $client) {
            VarDumper::dump('Default email template for '.$client->name);
            // Create an email template record
            $template = CreateEmailTemplate::run([
                'name' => $client->name."'s First Email Template (;",
                'client_id' => $client->id,
                'active' => 1,
                'markup' => $default_markup,
                'json' => '',//TODO
            ]);

            $template->active = 1;

            UpdateEmailTemplate::run($template->toArray());
        }
    }
}
