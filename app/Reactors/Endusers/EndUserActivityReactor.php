<?php

namespace App\Reactors\Endusers;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Endusers\Lead;
use App\Models\Endusers\LeadDetails;
use App\Models\Utility\AppState;
use App\StorableEvents\Endusers\Leads\LeadProfilePictureMoved;
use App\StorableEvents\Endusers\Leads\LeadUpdated;
use App\StorableEvents\Endusers\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\LeadWasTextMessagedByRep;
use App\StorableEvents\Endusers\NewLeadMade;
use App\StorableEvents\Endusers\SubscribedToAudience;
use App\StorableEvents\Endusers\UpdateLead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
    public function onNewLeadMade(NewLeadMade $event)
    {
        if(array_key_exists('profile_picture', $event->lead)){
            $file = $event->lead['profile_picture'];
            $destKey = "{$event->lead['client_id']}/{$file['uuid']}";
            Storage::disk('s3')->move($file['key'], $destKey);
            $file['key'] = $destKey;
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";

            LeadDetails::create([
                    'lead_id' => $event->lead['id'],
                    'client_id' => $event->lead['client_id'],
                    'field' => 'profile_picture',
                    'misc' => $file
                ]
            );
        }

        if(array_key_exists('middle_name', $event->lead)){
            $middle_name= $event->lead['middle_name'];
            LeadDetails::create([
                    'lead_id' => $event->lead['id'],
                    'client_id' => $event->lead['client_id'],
                    'field' => 'middle_name',
                    'value' => $middle_name,
                    'misc' => ['user' => $event->user ]
                ]
            );
        }



    }

    public function onUpdateLead(UpdateLead $event)
    {
        if(array_key_exists('profile_picture', $event->lead) && $event->lead['profile_picture'] !== null){
            $file = $event->lead['profile_picture'];
            $destKey = "{$event->lead['client_id']}/{$file['uuid']}";
            Storage::disk('s3')->move($file['key'], $destKey);
            $file['key'] = $destKey;
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";
            $profile_picture = LeadDetails::firstOrCreate([
                'lead_id' => $event->id,
                'client_id' => $event->lead['client_id'],
                'field' => 'profile_picture',
            ]);
            $profile_picture->misc =  $file;
            $profile_picture->save();
        }
    }

    public function onLeadUpdated(LeadUpdated $event)
    {
        $this->maybeMoveProfilePicture($event);
    }

    public function onLeadCreated(LeadCreated $event)
    {
        $this->maybeMoveProfilePicture($event);
    }

    protected function maybeMoveProfilePicture($event)
    {
        $file = $event->data['profile_picture'] ?? false;

        if ($file) {
            $destKey = "{$event->data['client_id']}/{$file['uuid']}";
            Storage::disk('s3')->move($file['key'], $destKey);
            $file['key'] = $destKey;
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";
            EndUserActivityAggregate::retrieve($event->aggregateRootUuid())->recordThat(new LeadProfilePictureMoved($file))->persist();
        }
    }
}
