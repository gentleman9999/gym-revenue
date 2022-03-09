<?php

namespace App\Reactors\Endusers;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Endusers\Lead;
use App\Models\Utility\AppState;
use App\StorableEvents\Endusers\Leads\LeadProfilePictureMoved;
use App\StorableEvents\Endusers\Leads\LeadUpdated;
use App\StorableEvents\Endusers\Leads\OldLeadProfilePictureDeleted;
use App\StorableEvents\Endusers\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\LeadWasTextMessagedByRep;
use App\StorableEvents\Endusers\SubscribedToAudience;
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
        if (!AppState::isSimuationMode()) {
            Mail::to($lead->email)->send(new EmailFromRep($event->data, $event->lead, $event->user));
        }
    }

    public function onLeadWasTextMessagedByRep(LeadWasTextMessagedByRep $event)
    {
        $lead = Lead::find($event->lead);
        $msg = $event->data['message'];

        if (!AppState::isSimuationMode()) {
            FireTwilioMsg::dispatch($lead->primary_phone, $msg)->onQueue('grp-' . env('APP_ENV') . '-jobs');
        }
    }

    public function onSubscribedToAudience(SubscribedToAudience $event)
    {
        // @todo - check the Campaigns the audience is attached to
        // @todo - if so, then trigger it here and its aggregate will deal
        // @todo - with whatever is supposed to happen.
    }

    public function onLeadUpdated(LeadUpdated $event)
    {
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->data, $event->oldData);
    }

    public function onLeadCreated(LeadCreated $event)
    {
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->data);
    }

    public function onLeadProfilePictureMoved(LeadProfilePictureMoved $event)
    {
        if(!$event->oldFile){
            return;
        }
        EndUserActivityAggregate::retrieve($event->aggregateRootUuid())->recordThat(new OldLeadProfilePictureDeleted($event->oldFile))->persist();
    }

    public function onOldLeadProfilePictureDeleted(OldLeadProfilePictureDeleted $event)
    {
        Storage::disk('s3')->delete($event->file['key']);
    }

    protected function maybeMoveProfilePicture($lead_id, $data, $oldData = null)
    {
        $file = $data['profile_picture'] ?? false;

        if (!$file) {
            return;
        }
        $destKey = "{$data['client_id']}/{$file['uuid']}";
        Storage::disk('s3')->move($file['key'], $destKey);
        $file['key'] = $destKey;
        $file['url'] = Storage::disk('s3')->url($file['key']);
        $aggy = EndUserActivityAggregate::retrieve($lead_id);
        if ($oldData['profile_picture']['misc'] ?? false) {
            $aggy->recordThat(new LeadProfilePictureMoved($file, $oldData['profile_picture']['misc']));
        } else {
            $aggy->recordThat(new LeadProfilePictureMoved($file));
        }
        $aggy->persist();

    }
}
