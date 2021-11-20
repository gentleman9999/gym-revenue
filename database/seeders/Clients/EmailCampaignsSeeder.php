<?php

namespace Database\Seeders\Clients;

use App\Actions\Clients\Activity\Comms\AssignEmailTemplateToCampaign;
use App\Models\Clients\Client;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Comms\EmailTemplates;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class EmailCampaignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VarDumper::dump('Default email campaign for Cape & Bay');
        $cnb_record = EmailCampaigns::firstOrCreate([
            'name' => "Baby's First Email Campaign (;",
            'active' => 0,
            'created_by_user_id' => 'auto'
        ]);
        $cnb_template = EmailTemplates::whereNull('client_id')->first();
        AssignEmailTemplateToCampaign::dispatch($cnb_template->id, $cnb_record->id, $cnb_record->created_by_user_id)->onQueue('grp-'.env('APP_ENV').'-jobs');

        $clients = Client::whereActive(1)->get();
        foreach ($clients as $client)
        {
            VarDumper::dump('Default email campaign for '.$client->name);

            $record = EmailCampaigns::firstOrCreate([
                'name' => $client->name."'s First Email Template D;",
                'client_id' => $client->id,
                'active' => 0,
                'created_by_user_id' => 'auto'
            ]);
            $template = EmailTemplates::whereClientId($client->id)->first();
            AssignEmailTemplateToCampaign::dispatch($template->id, $record->id, $record->created_by_user_id, $client->id)->onQueue('grp-'.env('APP_ENV').'-jobs');
        }
    }
}
