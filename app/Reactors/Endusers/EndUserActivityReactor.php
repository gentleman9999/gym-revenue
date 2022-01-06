<?php

namespace App\Reactors\Endusers;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Endusers\Lead;
use App\Models\Utility\AppState;
use App\StorableEvents\Endusers\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\LeadWasTextMessagedByRep;
use App\StorableEvents\Endusers\SubscribedToAudience;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EndUserActivityReactor extends Reactor implements ShouldQueue
{
    public function onLeadWasEmailedByRep(LeadWasEmailedByRep $event)
    {
        //Mail::to($addy)->send(new NewGrandOpeningLead($payload));
        $lead = Lead::find($event->lead);
        if(!AppState::isSimuationMode()){
            Mail::to($lead->email)->send(new EmailFromRep($event->data, $event->lead, $event->user));
        }
    }

    public function onLeadWasTextMessagedByRep(LeadWasTextMessagedByRep $event)
    {
        $lead = Lead::find($event->lead);
        $msg = $event->data['message'];

        if(!AppState::isSimuationMode()){
            FireTwilioMsg::dispatch($lead->primary_phone, $msg)->onQueue('grp-'.env('APP_ENV').'-jobs');
        }
    }

    public function onSubscribedToAudience(SubscribedToAudience $event)
    {
        // @todo - check the Campaigns the audience is attached to
        // @todo - if so, then trigger it here and its aggregate will deal
        // @todo - with whatever is supposed to happen.
    }
}
