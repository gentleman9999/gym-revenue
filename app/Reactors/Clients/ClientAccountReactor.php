<?php

namespace App\Reactors\Clients;

use App\Actions\Clients\Activity\Comms\FireOffEmailCampaign;
use App\Actions\Clients\Activity\Comms\FireOffSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignLaunched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientAccountReactor extends Reactor implements ShouldQueue
{
    public function onEmailCampaignLaunched(EmailCampaignLaunched $event){
        if(strtotime($event->date) <= strtotime('now')){
            //launch campaign
            FireOffEmailCampaign::dispatch($event->campaign);
        }else{
            //queue campaign
        }
    }
    public function onSmsCampaignLaunched(SmsCampaignLaunched $event){
        if(strtotime($event->date) <= strtotime('now')){
            //launch campaign
            FireOffSmsCampaign::dispatch($event->campaign);
        }else{
            //queue campaign
        }
    }
}
