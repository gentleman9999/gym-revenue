<?php

namespace App\Domain\Leads;

use App\Actions\Endusers\Members\CheckIfMemberWasLead;
use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Domain\Leads\Events\LeadCreated;
use App\Domain\Leads\Events\LeadProfilePictureMoved;
use App\Domain\Leads\Events\LeadUpdated;
use App\Domain\Leads\Events\LeadWasEmailedByRep;
use App\Domain\Leads\Events\LeadWasTextMessagedByRep;
use App\Domain\Leads\Events\OldLeadProfilePictureDeleted;
use App\Domain\Leads\Models\Lead;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Utility\AppState;
use App\StorableEvents\Endusers\Members\MemberCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class LeadActivityReactor extends Reactor implements ShouldQueue
{
    public function onLeadWasEmailedByRep(LeadWasEmailedByRep $event)
    {
        //Mail::to($addy)->send(new NewGrandOpeningLead($payload));
        $lead = Lead::find($event->aggregateRootUuid());
        if (! AppState::isSimuationMode() && ! $lead->unsubscribe_comms) {
            Mail::to($lead->email)->send(new EmailFromRep($event->payload, $event->aggregateRootUuid(), $event->payload['user']));
        }
    }

    public function onLeadWasTextMessagedByRep(LeadWasTextMessagedByRep $event)
    {
        $lead = Lead::find($event->aggregateRootUuid());
        $msg = $event->payload['message'];

        if (! AppState::isSimuationMode() && ! $lead->unsubscribe_comms) {
            FireTwilioMsg::dispatch($lead->primary_phone, $msg)->onQueue('grp-' . env('APP_ENV') . '-jobs');
        }
    }

    public function onLeadUpdated(LeadUpdated $event)
    {
        $aggy = LeadAggregate::retrieve($event->aggregateRootUuid());
        $oldData = $aggy->getOldData();
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->clientId(), $event->payload, $oldData);
    }

    public function onLeadCreated(LeadCreated $event)
    {
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->clientId(), $event->payload);
    }

    public function onLeadProfilePictureMoved(LeadProfilePictureMoved $event)
    {
        if (! $event->oldFile) {
            return;
        }
        LeadAggregate::retrieve($event->aggregateRootUuid())->recordThat(new OldLeadProfilePictureDeleted($event->oldFile))->persist();
    }

    public function onOldLeadProfilePictureDeleted(OldLeadProfilePictureDeleted $event)
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

    protected function maybeMoveProfilePicture(string $lead_id, string $client_id, array $data, array $oldData = null)
    {
        $file = $data['profile_picture'] ?? false;

        if (! $file) {
            return;
        }
        $destKey = "{$client_id}/{$file['uuid']}";
        Storage::disk('s3')->move($file['key'], $destKey);
        $file['key'] = $destKey;
        $file['url'] = Storage::disk('s3')->url($file['key']);
        $aggy = LeadAggregate::retrieve($lead_id);
        if ($oldData['profile_picture']['misc'] ?? false) {
            $aggy->recordThat(new LeadProfilePictureMoved($file, $oldData['profile_picture']['misc']));
        } else {
            $aggy->recordThat(new LeadProfilePictureMoved($file));
        }
        $aggy->persist();
    }
}
