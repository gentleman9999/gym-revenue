<?php

namespace Database\Seeders\Clients;

use App\Actions\Clients\Activity\Comms\AssignSMSTemplateToCampaign;
use App\Models\Clients\Client;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\SmsTemplates;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class SMSCampaignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VarDumper::dump('Default sms campaign for Cape & Bay');
        $cnb_record = SmsCampaigns::firstOrCreate([
            'name' => "Baby's First SMS Campaign (;",
            'active' => 0,
            'created_by_user_id' => 'auto'
        ]);
        $cnb_template = SmsTemplates::whereNull('client_id')->first();
        AssignSMSTemplateToCampaign::dispatch($cnb_template->id, $cnb_record->id, $cnb_record->created_by_user_id)->onQueue('grp-'.env('APP_ENV').'-jobs');

        $clients = Client::whereActive(1)->get();
        foreach ($clients as $client)
        {
            VarDumper::dump('Default sms campaign for '.$client->name);

            $record = SmsCampaigns::firstOrCreate([
                'name' => $client->name."'s First SMS Template D;",
                'client_id' => $client->id,
                'active' => 0,
                'created_by_user_id' => 'auto'
            ]);
            $template = SmsTemplates::whereClientId($client->id)->first();
            AssignSMSTemplateToCampaign::dispatch($template->id, $record->id, $record->created_by_user_id)->onQueue('grp-'.env('APP_ENV').'-jobs');
        }
    }
}
