<?php

namespace Database\Seeders\Comm;

use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\ScheduledCampaigns\Actions\CreateScheduledCampaign;
use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Users\Models\User;
use App\Services\Process;
use Illuminate\Database\Seeder;

class ScheduledCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** Modify the below date range to change when the calendar events will populate for testing since time doesn't stand still. */
        $start_date              = time();
        $end_date                = strtotime('+ 3 months');
        $daystep                 = 86400;
        $status                  = CampaignStatusEnum::cases();
        $process                 = Process::allocate(5);
        $num_of_events_to_create = env('QUICK_SEED') ? 2 : 5;
        $clients                 = Client::with('users', 'audiences')->whereActive(1)->get();

        // For each client
        foreach ($clients as $client) {
            echo("Default Scheduled Campaign for {$client->name}\n");
            $email_template = EmailTemplate::whereClientId($client['id'])->first()->id;
            $sms_template   = SmsTemplate::whereClientId($client['id'])->first()->id;
            $call_template  = CallScriptTemplate::whereClientId($client['id'])->whereUseOnce(false)->first()->id;

            $owner_id = null;
            foreach ($client->users as $user) {
                $rolesForUser = $user->getRoles();
                foreach ($rolesForUser as $role) {
                    if ($role === 'Account Owner') {
                        $owner_id = $user->id;
                    }
                }
            }

            if ($owner_id === null) {
                continue;
            }

            foreach ($client->audiences as $audience) {
                for ($i = 1; $i <= $num_of_events_to_create; $i++) {
                    $datebetween = abs(($end_date - $start_date) / $daystep);
                    $randomday   = rand(0, $datebetween);
                    $hour1       = rand(3, 12);
                    $title       = "Scheduled Campaign #{$i} for {$client->name}";
                    $start       = date('Y-m-d', $start_date + ($randomday * $daystep)) . ' ' . $hour1 . ':00:00';
                    // Create a Scheduled Campaign
                    $user     = new User();
                    $user->id = $owner_id;
                    $random   = rand(0, 3);
                    $process->queue([CreateScheduledCampaign::class, 'run'], [
                        'audience_id'       => $audience->id,
                        'name'              => $title,
                        'email_template_id' => $email_template,
                        'sms_template_id'   => $sms_template,
                        'call_template_id'  => $call_template,
                        'send_at'           => $start,
                        'status'            => $status[$random]->name,
                        'client_id'         => $client->id,
                    ], $user);
                }
            }
        }

        $process->run();
    }
}
