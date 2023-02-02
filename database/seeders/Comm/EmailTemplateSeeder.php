<?php

namespace Database\Seeders\Comm;

use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\EmailTemplates\Actions\CreateEmailTemplate;
use App\Domain\Templates\EmailTemplates\Actions\UpdateEmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //TODO: could create more pregenerated ones, and loop over directory
        $template = json_decode(file_get_contents(realpath(__DIR__ . "/../../../database/data/templates/email/basic.json")));

        $default_markup = $template->markup;
        $default_json   = $template->json;
        $json_string    = json_encode($default_json);
        $clients        = Client::whereActive(1)->get();
        // For each client
        foreach ($clients as $client) {
            echo("Default email template for {$client->name}\n");
            // Create an email template record
            $template = CreateEmailTemplate::run([
                'name'      => "{$client->name}'s First Email Template (;",
                'client_id' => $client->id,
                'active'    => 1,
                'markup'    => $default_markup,
                'json'      => $json_string,
                'subject'   => 'We should remove subject from templates and add it to the campaign/event',
            ]);

            UpdateEmailTemplate::run($template, ['active' => 1]);
        }
    }
}
