<?php

namespace App\Domain\Leads;

use App\Domain\Leads\Events\LeadClaimedByRep;
use App\Domain\Leads\Events\LeadConverted;
use App\Domain\Leads\Events\LeadCreated;
use App\Domain\Leads\Events\LeadDeleted;
use App\Domain\Leads\Events\LeadProfilePictureMoved;
use App\Domain\Leads\Events\LeadRestored;
use App\Domain\Leads\Events\LeadTrashed;
use App\Domain\Leads\Events\LeadUpdated;
use App\Domain\Leads\Events\LeadUpdatedCommunicationPreferences;
use App\Domain\Leads\Events\LeadWasCalledByRep;
use App\Domain\Leads\Events\LeadWasEmailedByRep;
use App\Domain\Leads\Events\LeadWasTextMessagedByRep;
use App\Domain\Leads\Events\TrialMembershipAdded;
use App\Domain\Leads\Events\TrialMembershipUsed;
use App\Domain\Leads\Models\Lead;
use App\Domain\Leads\Models\LeadDetails;
use App\Domain\Users\Models\User;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\TrialMembership;
use App\Models\Note;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LeadProjector extends Projector
{
    private $details = ['lead_status'];

    public function onLeadCreated(LeadCreated $event)
    {
        $lead = new Lead();
        //get only the keys we care about (the ones marked as fillable)
//        $lead_fillable_data = array_filter($event->payload, function ($key) {
//            return in_array($key, (new Lead())->getFillable());
//        }, ARRAY_FILTER_USE_KEY);
        // unsetting notes and lead status as these columns do not exist in leads table
        unset($event->payload['notes']);
        unset($event->payload['lead_status']);

        $profile_picture = json_encode($event->payload['profile_picture']);
        $lead->setRawAttributes($event->payload);


        $lead->profile_picture = $profile_picture;

        $lead->id = $event->aggregateRootUuid();
//        $lead->client_id = $event->payload['client_id'];
//        $lead->email = $event->payload['email'];
//        $lead->agreement_number = $event->payload['agreement_number'];

//        if (array_key_exists('agreement_number', $event->payload)) {
//            $lead->agreement_number = $event->payload['agreement_number'];
//        }

        $lead->save();

//        $created = new LeadDetails();
//        $created->forceFill([
//            'lead_id' => $lead->id,
//            'name' => $event->payload['first_name'],
//            'client_id' => $event->clientId(),
//            'field' => 'creates',
//            'value' => $event->modifiedBy(),
//        ]);
//        $created->save();
//
//        $creates = new LeadDetails();
//        $creates->forceFill([
//            'lead_id' => $lead->id,
//            'client_id' => $event->clientId(),
//            'field' => 'created',
//            'value' => $event->createdAt(),
//        ]);
//        $creates->save();

        $this->createOrUpdateLeadDetailsAndNotes($event, $lead);

//        if (array_key_exists('profile_picture', $event->payload) && $event->payload['profile_picture']) {
//            $file = $event->payload['profile_picture'];
//            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";
//
//            $profile = new LeadDetails();
//            $profile->forceFill([
//                'lead_id' => $lead->id,
//                'client_id' => $lead->client_id,
//                'field' => 'profile_picture',
//                'misc' => $file,
//            ]);
//            $profile->save();
//        }
    }

    public function onLeadUpdated(LeadUpdated $event)
    {
        $lead = Lead::withTrashed()->findOrFail($event->aggregateRootUuid());
        if (array_key_exists('email', $event->payload)) {
            $lead->email = $event->payload['email'];
        }
        $lead->fill($event->payload);
        $lead->save();
//        $updated = new LeadDetails();
//        $updated->forceFill([
//            'lead_id' => $lead->id,
//            'client_id' => $lead->client_id,
//            'field' => 'updated',
//            'value' => $event->modifiedBy(),
//        ]);
//        $updated->save();

        //TODO: see if we are still using this. I feel like we got rid of it.
        LeadDetails::whereLeadId($lead->id)->whereField('service_id')->delete();

        $this->createOrUpdateLeadDetailsAndNotes($event, $lead);

//        if (array_key_exists('profile_picture', $event->payload) && $event->payload['profile_picture']) {
//            $file = $event->payload['profile_picture'];
//            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";
//
//            $profile = LeadDetails::firstOrNew([
//                'lead_id' => $lead->id,
//                'client_id' => $lead->client_id,
//                'field' => 'profile_picture',
//            ], ['misc' => $file]);
//            $profile->client_id = $lead->client_id;
//            $profile->lead_id = $lead->id;
//            $profile->save();
//        }
    }

    public function onLeadTrashed(LeadTrashed $event)
    {
        $lead = Lead::findOrFail($event->aggregateRootUuid());
        $lead->deleteOrFail();

        $deleted = new LeadDetails();
        $deleted->forceFill([
            'client_id' => $lead->client_id,
            'lead_id' => $lead->id,
            'field' => 'softdelete',
            'value' => $event->reason,
            'misc' => ['userid' => $event->modifiedBy()],
        ]);
        $deleted->save();
    }

    public function onLeadRestored(LeadRestored $event)
    {
        Lead::withTrashed()->findOrFail($event->aggregateRootUuid())->restore();
    }

    public function onLeadDeleted(LeadDeleted $event)
    {
        Lead::withTrashed()->findOrFail($event->aggregateRootUuid())->forceDelete();
    }

    public function onLeadClaimedByRep(LeadClaimedByRep $event)
    {
        $lead = Lead::withTrashed()->findOrFail($event->aggregateRootUuid());
        $lead->owner_user_id = $event->claimedByUserId;
        $lead->save();
    }

    public function onLeadWasEmailedByRep(LeadWasEmailedByRep $event)
    {
        $lead = Lead::find($event->aggregateRootUuid());
        $user = User::find($event->payload['user']);

        $misc = $event->payload;
        $misc['user_email'] = $user->email;

        $emailed = new LeadDetails();
        $emailed->forceFill([
            'client_id' => $lead->client_id,
            'lead_id' => $event->aggregateRootUuid(),
            'field' => 'emailed_by_rep',
            'value' => $event->payload['user'],
            'misc' => $misc,
        ]);
        $emailed->save();
    }

    public function onLeadWasTextMessagedByRep(LeadWasTextMessagedByRep $event)
    {
        $lead = Lead::find($event->aggregateRootUuid());
        $user = User::find($event->payload['user']);

        $misc = $event->payload;
        $misc['user_email'] = $user->email;

        $text_messaged = new LeadDetails();
        $text_messaged->forceFill([
            'client_id' => $lead->client_id,
            'lead_id' => $lead->id,
            'field' => 'sms_by_rep',
            'value' => $event->payload['user'],
            'misc' => $misc,
        ]);
        $text_messaged->save();
    }

    public function onLeadWasCalledByRep(LeadWasCalledByRep $event)
    {
        $lead = Lead::find($event->aggregateRootUuid());
        $user = User::find($event->payload['user']);

        $misc = $event->payload;
        $misc['user_email'] = $user->email;

        $called = new LeadDetails();
        $called->forceFill([
            'client_id' => $lead->client_id,
            'lead_id' => $lead->id,
            'field' => 'called_by_rep',
            'value' => $event->payload['user'],
            'misc' => $misc,
        ]);
        $called->save();

        $notes = $misc['notes'] ?? false;
        if ($notes) {
            Note::create([
                'entity_id' => $lead->id,
                'entity_type' => Lead::class,
                'note' => $notes,
                'created_by_user_id' => $event->payload['user'],
            ]);
        }
    }

    public function onTrialMembershipAdded(TrialMembershipAdded $event)
    {
        $lead = Lead::findOrFail($event->aggregateRootUuid());
        $trial = TrialMembershipType::find($event->trial);
        $client_id = $lead->client_id;
        TrialMembership::create([
            'client_id' => $client_id,
            'type_id' => $event->trial,
            'lead_id' => $lead->id,
            'start_date' => $event->createdAt(),
            'expiry_date' => $event->createdAt()->addDays($trial->trial_length),
            'location_id' => $lead->gr_location_id,
            'active' => 1,
        ]);

        $trail_started = new LeadDetails();
        $trail_started->forceFill([
            'client_id' => $client_id,
            'lead_id' => $lead->id,
            'field' => 'trial-started',
            'value' => $event->createdAt(),
            'misc' => ['trial_id' => $event->trial, 'date' => $event->createdAt(), 'client' => $client_id],
        ]);
        $trail_started->save();
    }

    public function onTrialMembershipUsed(TrialMembershipUsed $event)
    {
        $trial_used = new LeadDetails();
        $trial_used->forceFill([
            'client_id' => $event->clientId(),
            'lead_id' => $event->aggregateRootUuid(),
            'field' => 'trial-used',
            'value' => $event->trial,
            'misc' => ['trial_id' => $event->trial, 'date' => $event->createdAt(), 'client' => $event->clientId()],
        ]);
        $trial_used->save();
    }

    public function onLeadConverted(LeadConverted $event)
    {
        $record = lead::withTrashed()->findOrFail($event->aggregateRootUuid());
        $record->forceFill(['converted_at' => $event->createdAt(), 'member_id' => $event->member])->save();
    }

    public function onLeadProfilePictureMoved(LeadProfilePictureMoved $event)
    {
        Lead::findOrFail($event->aggregateRootUuid())->updateOrFail(['profile_picture' => $event->file]);
    }

    protected function createOrUpdateLeadDetailsAndNotes($event, $lead)
    {
        foreach ($this->details as $field) {
            LeadDetails::createOrUpdateRecord($event->aggregateRootUuid(), $lead->client_id, $field, $event->payload[$field] ?? null);
        }

        $notes = $event->payload['notes'] ?? false;
        if ($notes && $notes['title'] ?? false) {
            Note::create([
                'entity_id' => $lead->id,
                'entity_type' => Lead::class,
                'title' => $notes['title'],
                'note' => $notes['note'],
                'created_by_user_id' => $event->modifiedBy(),
            ]);

            $noted_created = new LeadDetails();
            $noted_created->forceFill([
                'lead_id' => $lead->id,
                'client_id' => $lead->client_id,
                'field' => 'note_created',
                'value' => $notes['note'],
            ]);
            $noted_created->save();
        }

        foreach ($event->lead['services'] ?? [] as $service_id) {
            $service = new LeadDetails();
            $service->forceFill([
                'lead_id' => $event->aggregateRootUuid(),
                'client_id' => $lead->client_id,
                'field' => 'service_id',
                'value' => $service_id,
            ]);
            $service->save();
        }
    }

    public function onLeadUpdatedCommunicationPreferences(LeadUpdatedCommunicationPreferences $event)
    {
        Lead::withTrashed()->findOrFail($event->aggregateRootUuid())->forceFill(['unsubscribed_email' => $event->email, 'unsubscribed_sms' => $event->sms])->save();
    }
}
