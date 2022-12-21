<?php

namespace Database\Seeders\Comm;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\ScheduledCampaigns\Actions\CreateScheduledCampaign;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Users\Models\User;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class ScheduledCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $amountOfEvents = 5;
        if (env('QUICK_SEED')) {
            $amountOfEvents = 2;
        }
        // Get all the Clients
        VarDumper::dump('Getting Clients for Scheduled Campaigns');
        $clients = Client::whereActive(1)->get();

        /** Modify the below date range to change when the calendar events will populate for testing since time doesn't stand still. */
        $datestart = strtotime(date('Y-m-d'));
        $dateend = strtotime(date('Y-m-d', strtotime(' + 3 months')));
        $daystep = 86400;

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
            VarDumper::dump('Default Scheduled Campaign for '.$client->name);
            $audiences = Audience::whereClientId($client['id'])->get()->toArray();
            $email_template = EmailTemplate::whereClientId($client['id'])->first()->toArray()['id'];
            $sms_template = SmsTemplate::whereClientId($client['id'])->first()->toArray()['id'];
            $call_template = CallScriptTemplate::whereClientId($client['id'])->whereUseOnce(false)->first()->toArray()['id'];
            $users = User::whereClientId($client['id'])->get();
            $owner_id = null;
            foreach ($users as $user) {
                $rolesForUser = $user->getRoles();
                foreach ($rolesForUser as $role) {
                    if ($role == "Account Owner") {
                        $owner_id = $user->id;
                    }
                }
            }
            if (! is_null($owner_id)) {
                if (env('RAPID_SEED') == true) {
                    $location = Location::whereClientId($client['id'])->first()->toArray();
                    $location_id = $location['id'];
                } else {
                    $locations = Location::whereClientId($client['id'])->get()->toArray();
                    $location_id = $locations[rand(0, count($locations) - 1)]['id'];
                }

                foreach ($audiences as $audience) {
                    for ($i = 1; $i <= $amountOfEvents; $i++) {
                        $datebetween = abs(($dateend - $datestart) / $daystep);
                        $randomday = rand(0, $datebetween);
                        $hour1 = rand(3, 12);
                        $title = 'Scheduled Campaign #' . $i . ' for ' . $client->name;
                        $start = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour1 . ':00:00';
                        // Create a Scheduled Campaign
                        $user = new User();
                        $user->id = $owner_id;
                        $user->current_location_id = $location_id;
                        $status = collect(CampaignStatusEnum::cases());
                        $random = rand(0, 3);
                        $campaign = CreateScheduledCampaign::run([
                            'audience_id' => $audience['id'],
                            'name' => $title,
                            'email_template_id' => $email_template,
                            'sms_template_id' => $sms_template,
                            'call_template_id' => $call_template,
                            'send_at' => $start,
                            'status' => $status[$random]->name,
                            'client_id' => $client['id'],
                        ], $user);
                    }
                }
            }
        }
    }
}
