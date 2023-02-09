<?php

namespace Database\Seeders\Comm;

use App\Domain\Campaigns\DripCampaigns\Actions\CreateDripCampaign;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Services\Process;
use Illuminate\Database\Seeder;
use NumberFormatter;

class DripCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $num_of_events_to_create = env('QUICK_SEED') ? 2 : 5;
        $clients = Client::with('audiences')->whereActive(1)->get();
        $word = new NumberFormatter("en", NumberFormatter::ORDINAL);
        $status = CampaignStatusEnum::cases();
        $process = Process::allocate(5);

        // For each client
        foreach ($clients as $client) {
            $email_t = EmailTemplate::whereClientId($client['id'])->first()->id;
            $sms_t = SmsTemplate::whereClientId($client['id'])->first()->id;
            $call_t = CallScriptTemplate::whereClientId($client['id'])->whereUseOnce(false)->first()->id;

            foreach ($client->audiences as $audience) {
                for ($i = 1; $i <= $num_of_events_to_create; $i++) {
                    $drip_days = rand(0, 6);
                    $days[0] = [
                        'day_in_campaign' => 0,
                        'email' => $email_t,
                        'sms' => $sms_t,
                        'call' => $call_t,
                        'date' => false,
                    ];

                    for ($d = 1; $d <= $drip_days; $d++) {
                        if (rand(0, 1) === 1) {
                            if (rand(0, 1) === 1) {
                                $email_template = $email_t;
                            } else {
                                $email_template = "";
                            }
                            if (rand(0, 1) === 1) {
                                $sms_template = $sms_t;
                            } else {
                                $sms_template = "";
                            }
                            if (rand(0, 1) === 1) {
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

                    $random = rand(0, 3);
                    $process->queue([CreateDripCampaign::class, 'run'], [
                        'name' => "{$client->name}'s {$word->format($i)} {$audience['name']} Drip Campaign (;",
                        'client_id' => $client->id,
                        'audience_id' => $audience['id'],
                        'status' => $status[$random]->name,
                        'days' => $days,
                    ]);
                }
            }
        }

        $process->run();
    }
}
