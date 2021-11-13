<?php

namespace App\Reactors\Endusers;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Endusers\Lead;
use App\StorableEvents\Endusers\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\LeadWasTextMessagedByRep;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EndUserActivityReactor extends Reactor implements ShouldQueue
{
    public function onLeadWasEmailedByRep(LeadWasEmailedByRep $event)
    {
        //Mail::to($addy)->send(new NewGrandOpeningLead($payload));
        $lead = Lead::find($event->lead);
        Mail::to($lead->email)->send(new EmailFromRep($event->data, $event->lead, $event->user));
    }

    public function onLeadWasTextMessagedByRep(LeadWasTextMessagedByRep $event)
    {
        $lead = Lead::find($event->lead);
        $msg = $event->data['message'];

        FireTwilioMsg::dispatch($lead->mobile_phone, $msg)->onQueue('grp-'.env('APP_ENV').'-jobs');
    }
}
