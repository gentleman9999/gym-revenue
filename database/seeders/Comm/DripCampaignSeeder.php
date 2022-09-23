<?php

namespace Database\Seeders\Comm;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\DripCampaigns\Actions\CreateDripCampaign;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use Illuminate\Database\Seeder;
use NumberFormatter;
use Symfony\Component\VarDumper\VarDumper;

class DripCampaignSeeder extends Seeder
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
        VarDumper::dump('Getting Clients for Drip campaigns');

        /** Modify the below date range to change when the calendar events will populate for testing since time doesn't stand still. */
        $datestart = strtotime(date('Y-m-d'));
        $dateend = strtotime(date('Y-m-d', strtotime(' + 3 months')));
        $daystep = 86400;

        $clients = Client::whereActive(1)->get();
        // For each client
        foreach ($clients as $client) {
            $audiences = Audience::whereClientId($client['id'])->get();
            // Create an email template record
            $email_t = EmailTemplate::whereClientId($client['id'])->first()->id;
            $sms_t = SmsTemplate::whereClientId($client['id'])->first()->id;
            $call_t = CallScriptTemplate::whereClientId($client['id'])->first()->id;
            foreach ($audiences as $audience) {
                for ($i = 1; $i <= $amountOfEvents; $i++) {
                    $datebetween = abs(($dateend - $datestart) / $daystep);
                    $randomday = rand(0, $datebetween);
                    $hour1 = rand(3, 12);
                    $hour2 = $hour1 + rand(1, 2);
                    $start = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour1 . ':00:00';
                    $end = date("Y-m-d", $datestart + ($randomday * $daystep)) . ' ' . $hour2 . ':00:00';
                    $dripDays = rand(0, 6);
                    $days[0] = [
                        'day_in_campaign' => 0,
                        'email' => $email_t,
                        'sms' => $sms_t,
                        'call' => $call_t,
                        'date' => false,
                        ];
                    for ($d = 1; $d <= $dripDays; $d++) {
                        if (rand(0, 1) == 1) {
                            if (rand(0, 1) == 1) {
                                $email_template = $email_t;
                            } else {
                                $email_template = "";
                            }
                            if (rand(0, 1) == 1) {
                                $sms_template = $sms_t;
                            } else {
                                $sms_template = "";
                            }
                            if (rand(0, 1) == 1) {
                                $call_template = $call_t;
                            } else {
                                $call_template = "";
                            }

                            $day = [
                                'day_in_campaign' => $d,
                                'email' => $email_template,
                                'sms' => $sms_template,
                                'call' => $call_template,
                                'date' => false,
                            ];
                            $days[$d] = $day;
                        }
                    }
                    $word = new NumberFormatter("en", NumberFormatter::ORDINAL);
                    $status = collect(CampaignStatusEnum::cases());
                    $random = rand(0, 3);
                    $dripCampaign = [
                        'name' => $client->name . "'s " . $word->format($i) . " " . $audience['name'] . " Drip Campaign (;",
                        'client_id' => $client->id,
                        'audience_id' => $audience['id'],
//                        'start_at' => $start,
//                        'end_at' => $end,
                        'status' => $status[$random]->name,
                        'days' => $days,
                    ];
                    CreateDripCampaign::run($dripCampaign);
                }
            }
        }
    }
}
