<?php

namespace App\Reactors\Endusers;

use App\Actions\Endusers\Members\CheckIfMemberWasLead;
use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Aggregates\Endusers\LeadAggregate;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Endusers\Lead;
use App\Models\Utility\AppState;
use App\StorableEvents\Endusers\Leads\LeadProfilePictureMoved;
use App\StorableEvents\Endusers\Leads\LeadUpdated;
use App\StorableEvents\Endusers\Leads\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\Members\MemberCreated;
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
        if (! AppState::isSimuationMode() && ! $lead->unsubscribe_comms) {
            Mail::to($lead->email)->send(new EmailFromRep($event->data, $event->lead, $event->user));
        }
    }

    public function onLeadWasTextMessagedByRep(\App\StorableEvents\Endusers\Leads\LeadWasTextMessagedByRep $event)
    {
        $lead = Lead::find($event->lead);
        $msg = $event->data['message'];

        if (! AppState::isSimuationMode() && ! $lead->unsubscribe_comms) {
            FireTwilioMsg::dispatch($lead->primary_phone, $msg)->onQueue('grp-' . env('APP_ENV') . '-jobs');
        }
    }

    public function onLeadUpdated(LeadUpdated $event)
    {
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->data, $event->oldData);
    }

    public function onLeadCreated(LeadCreated $event)
    {
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->data);
    }

    public function onLeadProfilePictureMoved(\App\StorableEvents\Endusers\Leads\LeadProfilePictureMoved $event)
    {
        if (! $event->oldFile) {
            return;
        }
        LeadAggregate::retrieve($event->aggregateRootUuid())->recordThat(new \App\StorableEvents\Endusers\Leads\OldLeadProfilePictureDeleted($event->oldFile))->persist();
    }

    public function onOldLeadProfilePictureDeleted(\App\StorableEvents\Endusers\Leads\OldLeadProfilePictureDeleted $event)
    {
        Storage::disk('s3')->delete($event->file['key']);
    }

    public function onMemberCreated(MemberCreated $event)
    {
        CheckIfMemberWasLead::run([
            'member_id' => $event->data['id'],
            'email' => $event->data['email'],
        ]);
    }

    protected function maybeMoveProfilePicture($lead_id, $data, $oldData = null)
    {
        $file = $data['profile_picture'] ?? false;

        if (! $file) {
            return;
        }
        $destKey = "{$data['client_id']}/{$file['uuid']}";
        Storage::disk('s3')->move($file['key'], $destKey);
        $file['key'] = $destKey;
        $file['url'] = Storage::disk('s3')->url($file['key']);
        $aggy = LeadAggregate::retrieve($lead_id);
        if ($oldData['profile_picture']['misc'] ?? false) {
            $aggy->recordThat(new \App\StorableEvents\Endusers\Leads\LeadProfilePictureMoved($file, $oldData['profile_picture']['misc']));
        } else {
            $aggy->recordThat(new LeadProfilePictureMoved($file));
        }
        $aggy->persist();
    }
}
