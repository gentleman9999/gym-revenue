<?php

namespace App\Projectors\Endusers;

use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\AudienceMember;
use App\Models\Endusers\Lead;
use App\Models\Endusers\LeadDetails;
use App\Models\Endusers\Member;
use App\Models\Endusers\TrialMembership;
use App\Models\Note;
use App\Models\User;
use App\StorableEvents\Endusers\LeadDetailUpdated;
use App\StorableEvents\Endusers\Leads\LeadCreated;
use App\StorableEvents\Endusers\Leads\LeadDeleted;
use App\StorableEvents\Endusers\Leads\LeadProfilePictureMoved;
use App\StorableEvents\Endusers\Leads\LeadRestored;
use App\StorableEvents\Endusers\Leads\LeadTrashed;
use App\StorableEvents\Endusers\Leads\LeadUpdated;
use App\StorableEvents\Endusers\Leads\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\Leads\SubscribedToAudience;
use App\StorableEvents\Endusers\Leads\TrialMembershipAdded;
use App\StorableEvents\Endusers\Members\MemberCreated;
use App\StorableEvents\Endusers\Members\MemberRestored;
use App\StorableEvents\Endusers\Members\MemberTrashed;
use App\StorableEvents\Endusers\Members\MemberUpdated;
use Carbon\Carbon;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EndUserActivityProjector extends Projector
{
    private $details = ['middle_name', 'lead_status', 'dob', 'opportunity'];

    public function onLeadClaimedByRep(\App\StorableEvents\Endusers\Leads\LeadClaimedByRep $event)
    {
        $lead = LeadDetails::firstOrCreate([
            'client_id' => $event->client,
            'lead_id' => $event->lead,
            'field' => 'claimed',
        ]);

        $lead->value = $event->user;
        $misc = $lead->misc;
        if (!is_array($misc)) {
            $misc = [];
        }

        if (!array_key_exists('claim_date', $misc)) {
            $misc['claim_date'] = date('Y-m-d');
        }

        $user = User::find($event->user);
        $misc['user_id'] = $user->email;
        $lead->misc = $misc;
        $lead->save();
    }

    public function onLeadWasEmailedByRep(LeadWasEmailedByRep $event)
    {
        $lead = Lead::find($event->lead);
        $user = User::find($event->user);

        $misc = $event->data;
        $misc['user_email'] = $user->email;
        LeadDetails::firstOrCreate([
            'client_id' => $lead->client_id,
            'lead_id' => $event->lead,
            'field' => 'emailed_by_rep',
            'value' => $event->user,
            'misc' => $misc,
        ]);
    }

    public function onLeadWasTextMessagedByRep(\App\StorableEvents\Endusers\Leads\LeadWasTextMessagedByRep $event)
    {
        $lead = Lead::find($event->lead);
        $user = User::find($event->user);

        $misc = $event->data;
        $misc['user_email'] = $user->email;
        LeadDetails::firstOrCreate([
            'client_id' => $lead->client_id,
            'lead_id' => $event->lead,
            'field' => 'sms_by_rep',
            'value' => $event->user,
            'misc' => $misc,
        ]);

    }

    public function onLeadWasCalledByRep(\App\StorableEvents\Endusers\Leads\LeadWasCalledByRep $event)
    {
        $lead = Lead::find($event->lead);
        $user = User::find($event->user);

        $misc = $event->data;
        $misc['user_email'] = $user->email;
        LeadDetails::firstOrCreate([
            'client_id' => $lead->client_id,
            'lead_id' => $event->lead,
            'field' => 'called_by_rep',
            'value' => $event->user,
            'misc' => $misc,
        ]);

        $notes = $misc['notes'] ?? false;
        if ($notes) {
            Note::create([
                'entity_id' => $event->lead,
                'entity_type' => Lead::class,
                'note' => $notes,
                'created_by_user_id' => $event->user
            ]);
        }
    }

    public function onSubscribedToAudience(SubscribedToAudience $event)
    {
        $audience_record = CommAudience::whereClientId($event->client)
            ->whereSlug($event->audience)->whereActive(1)->first();

        if (!is_null($audience_record)) {
            // add a new record to audience_members
            $audience_member_record = AudienceMember::firstOrCreate([
                'client_id' => $event->client,
                'audience_id' => $audience_record->id,
                'entity_id' => $event->user,
                'entity_type' => $event->entity,
                'subscribed' => true,
                'dnc' => false
            ]);

            // add a new record to entity's details
            $entity_model = new $event->entity;
            $details_class = $entity_model::getDetailsTable();
            $details_model = new $details_class();
            $details_model->firstOrCreate([
                'client_id' => $event->client,
                $details_model->fk => $event->user,
                'field' => 'audience_subscribed',
                'value' => $audience_record->id,
                'misc' => [
                    'date' => date('Y-m-d'),
                    'audience_member_record' => $audience_member_record->id
                ],
            ]);
        }

    }

    public function onTrialMembershipAdded(TrialMembershipAdded $event)
    {
        $lead = Lead::findOrFail($event->lead);
        $trial = TrialMembershipType::find($event->trial);
        $client_id = $lead->client_id;
        TrialMembership::create([
            'client_id' => $client_id,
            'type_id' => $event->trial,
            'lead_id' => $event->lead,
            'start_date' => $event->date,
            'expiry_date' => Carbon::instance(new \DateTime($event->date))->addDays($trial->trial_length),
            'club_id' => $lead->gr_location_id,
            'active' => 1
        ]);
        LeadDetails::create([
            'client_id' => $event->client,
            'lead_id' => $event->lead,
            'field' => 'trial-started',
            'value' => $event->date,
            'misc' => ['trial_id' => $event->trial, 'date' => $event->date, 'client' => $event->client]
        ]);
    }

    public function onTrialMembershipUsed(\App\StorableEvents\Endusers\Leads\TrialMembershipUsed $event)
    {
        $lead = Lead::findOrFail($event->lead);

        LeadDetails::create([
            'client_id' => $event->client,
            'lead_id' => $event->lead,
            'field' => 'trial-used',
            'value' => $event->trial,
            'misc' => ['trial_id' => $event->trial, 'date' => $event->date, 'client' => $event->client]
        ]);
    }


    ///new lead projections
    public function onLeadCreated(LeadCreated $event)
    {
        //get only the keys we care about (the ones marked as fillable)
        $lead_table_data = array_filter($event->data, function ($key) {
            return in_array($key, (new Lead)->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $lead = Lead::create($lead_table_data);

        $user = User::find($event->user);

        LeadDetails::create([
            'lead_id' => $lead->id,
            'client_id' => $event->data['client_id'],
            'field' => 'creates',
            'value' => $user === null ? 'Auto Generated' : $user->email,
        ]);
        LeadDetails::create([
            'lead_id' => $lead->id,
            'client_id' => $event->data['client_id'],
            'field' => 'created',
            'value' => Carbon::now()
        ]);
        LeadDetails::create([
            'lead_id' => $lead->id,
            'client_id' => $event->data['client_id'],
            'field' => 'agreement_number',
            'value' => floor(time() - 99999999),
        ]);

        $this->createOrUpdateLeadDetailsAndNotes($event, $lead);

        if (array_key_exists('profile_picture', $event->data) && $event->data['profile_picture']) {
            $file = $event->data['profile_picture'];
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";

            LeadDetails::create([
                    'lead_id' => $lead->id,
                    'client_id' => $lead->client_id,
                    'field' => 'profile_picture',
                    'misc' => $file
                ]
            );
        }
    }

    public function onLeadUpdated(LeadUpdated $event)
    {
        $lead = Lead::withTrashed()->findOrFail($event->data['id']);
        $lead->updateOrFail($event->data);

        $user = User::find($event->user);
        LeadDetails::create([
            'lead_id' => $lead->id,
            'client_id' => $lead->client_id,
            'field' => 'updated',
            'value' => $user->email,
        ]);

        //TODO: see if we are still using this. I feel like we got rid of it.
        LeadDetails::whereLeadId($lead->id)->whereField('service_id')->delete();

        $this->createOrUpdateLeadDetailsAndNotes($event, $lead);

        if (array_key_exists('profile_picture', $event->data) && $event->data['profile_picture']) {
            $file = $event->data['profile_picture'];
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";

            LeadDetails::firstOrCreate([
                    'lead_id' => $lead->id,
                    'client_id' => $lead->client_id,
                    'field' => 'profile_picture',
                ]
            )->updateOrFail(['misc' => $file]);
        }
    }

    public function onLeadTrashed(LeadTrashed $event)
    {
        $lead = Lead::findOrFail($event->id);
        $lead->deleteOrFail();
        LeadDetails::create([
            'client_id' => $lead->client_id,
            'lead_id' => $lead->id,
            'field' => 'softdelete',
            'value' => $event->reason,
            'misc' => ['userid' => $event->user]
        ]);
    }

    public function onLeadRestored(LeadRestored $event)
    {
        Lead::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onLeadDeleted(LeadDeleted $event)
    {
        Lead::withTrashed()->findOrFail($event->id)->forceDelete();
    }

    public function onLeadProfilePictureMoved(LeadProfilePictureMoved $event)
    {
        LeadDetails::whereLeadId($event->aggregateRootUuid())->whereField('profile_picture')->firstOrFail()->updateOrFail(['misc' => $event->file]);
    }

    protected function createOrUpdateLeadDetailsAndNotes($event, $lead)
    {
        foreach ($this->details as $field) {
            LeadDetails::createOrUpdateRecord($event->data['id'], $event->data['client_id'], $field, $event->data[$field] ?? null);
        }

        $notes = $event->data['notes'] ?? false;
        if ($notes) {
            Note::create([
                'entity_id' => $lead->id,
                'entity_type' => Lead::class,
                'note' => $notes,
                'created_by_user_id' => $event->user
            ]);
            LeadDetails::create([
                'lead_id' => $lead->id,
                'client_id' => $lead->client_id,
                'field' => 'note_created',
                'value' => $notes,
            ]);
        }

        foreach ($event->lead['services'] ?? [] as $service_id) {
            LeadDetails::create([
                    'lead_id' => $event->aggregateRootUuid(),
                    'client_id' => $lead->client_id,
                    'field' => 'service_id',
                    'value' => $service_id
                ]
            );
        }
    }

    //member projections
    public function onMemberCreated(MemberCreated $event)
    {
        //get only the keys we care about (the ones marked as fillable)
        $member_table_data = array_filter($event->data, function ($key) {
            return in_array($key, (new Member)->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $member = Member::create($member_table_data);

//        $user = User::find($event->user);

//        MemberDetails::create([
//            'member_id' => $member->id,
//            'client_id' => $event->data['client_id'],
//            'field' => 'creates',
//            'value' => $user === null ? 'Auto Generated' : $user->email,
//        ]);
//        MemberDetails::create([
//            'member_id' => $member->id,
//            'client_id' => $event->data['client_id'],
//            'field' => 'created',
//            'value' => Carbon::now()
//        ]);
//        MemberDetails::create([
//            'member_id' => $member->id,
//            'client_id' => $event->data['client_id'],
//            'field' => 'agreement_number',
//            'value' => floor(time() - 99999999),
//        ]);

        $this->createOrUpdateMemberDetailsAndNotes($event, $member);

    }

    public function onMemberUpdated(MemberUpdated $event)
    {
        $member = Member::withTrashed()->findOrFail($event->data['id']);
        $member->updateOrFail($event->data);

//        $user = User::find($event->user);
//        MemberDetails::create([
//            'member_id' => $member->id,
//            'client_id' => $member->client_id,
//            'field' => 'updated',
//            'value' => $user->email,
//        ]);

        //TODO: see if we are still using this. I feel like we got rid of it.
//        MemberDetails::whereMemberId($member->id)->whereField('service_id')->delete();

        $this->createOrUpdateMemberDetailsAndNotes($event, $member);
    }

    public function onMemberTrashed(MemberTrashed $event)
    {
        $member = Member::findOrFail($event->id);
        $member->deleteOrFail();
//        MemberDetails::create([
//            'client_id' => $member->client_id,
//            'member_id' => $member->id,
//            'field' => 'softdelete',
//            'value' => $event->reason,
//            'misc' => ['userid' => $event->user]
//        ]);
    }

    public function onMemberRestored(MemberRestored $event)
    {
        Member::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onMemberDeleted(MemberDeleted $event)
    {
        Member::withTrashed()->findOrFail($event->id)->forceDelete();
    }

//    public function onMemberProfilePictureMoved(MemberProfilePictureMoved $event)
//    {
//        MemberDetails::whereMemberId($event->aggregateRootUuid())->whereField('profile_picture')->firstOrFail()->updateOrFail(['misc' => $event->file]);
//    }

    protected function createOrUpdateMemberDetailsAndNotes($event, $member)
    {
//        foreach ($this->details as $field) {
//            MemberDetails::createOrUpdateRecord($event->data['id'], $event->data['client_id'], $field, $event->data[$field] ?? null);
//        }

        $notes = $event->data['notes'] ?? false;
        if ($notes) {
            Note::create([
                'entity_id' => $member->id,
                'entity_type' => Member::class,
                'note' => $notes,
                'created_by_user_id' => $event->user
            ]);
//            MemberDetails::create([
//                'member_id' => $member->id,
//                'client_id' => $member->client_id,
//                'field' => 'note_created',
//                'value' => $notes,
//            ]);
        }

//        foreach ($event->member['services'] ?? [] as $service_id) {
//            MemberDetails::create([
//                    'member_id' => $event->aggregateRootUuid(),
//                    'client_id' => $member->client_id,
//                    'field' => 'service_id',
//                    'value' => $service_id
//                ]
//            );
//        }
    }

}
