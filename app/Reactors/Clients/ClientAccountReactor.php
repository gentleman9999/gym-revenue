<?php

namespace App\Reactors\Clients;

use App\Actions\Clients\Activity\Comms\FireOffEmailCampaign;
use App\Actions\Clients\Activity\Comms\FireOffSmsCampaign;
use App\Models\Comms\QueuedEmailCampaign;
use App\Models\Comms\QueuedSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignLaunched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientAccountReactor extends Reactor implements ShouldQueue
{
    public function onEmailCampaignLaunched(EmailCampaignLaunched $event)
    {
        if (strtotime($event->date) <= strtotime('now')) {
            //launch campaign
            FireOffEmailCampaign::dispatch($event->campaign);
        } else {
            //queue campaign
            QueuedEmailCampaign::create(['email_campaign_id' => $event->campaign, 'trigger_at' => $event->date]);
        }
    }

    public function onSmsCampaignLaunched(SmsCampaignLaunched $event)
    {
        if (strtotime($event->date) <= strtotime('now')) {
            //launch campaign
            FireOffSmsCampaign::dispatch($event->campaign);
        } else {
            //queue campaign
            QueuedSmsCampaign::create(['sms_campaign_id' => $event->campaign, 'trigger_at' => $event->date]);
        }
    }
}
