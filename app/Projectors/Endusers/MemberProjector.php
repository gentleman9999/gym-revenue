<?php

namespace App\Projectors\Endusers;

use App\Models\Endusers\Member;
use App\Models\Note;
use App\Models\User;
use App\StorableEvents\Endusers\Members\MemberCreated;
use App\StorableEvents\Endusers\Members\MemberRestored;
use App\StorableEvents\Endusers\Members\MemberSubscribedToComms;
use App\StorableEvents\Endusers\Members\MemberTrashed;
use App\StorableEvents\Endusers\Members\MemberUnsubscribedFromComms;
use App\StorableEvents\Endusers\Members\MemberUpdated;
use Carbon\Carbon;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class MemberProjector extends Projector
{
    //member projections
    public function onMemberCreated(MemberCreated $event)
    {
        //get only the keys we care about (the ones marked as fillable)
        $member_table_data = array_filter($event->data, function ($key) {
            return in_array($key, (new Member())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $member_table_data['agreement_number'] = floor(time() - 99999999);
        if (array_key_exists('profile_picture', $event->data) && $event->data['profile_picture']) {
            $file = $event->data['profile_picture'];
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";
            $member_table_data['profile_picture'] = $file;
        }
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
        if (array_key_exists('profile_picture', $event->data) && $event->data['profile_picture']) {
            $file = $event->data['profile_picture'];
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";
            $event->data['profile_picture'] = $file;
        }
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
                'title' => $notes['title'],
                'note' => $notes['note'],
                'created_by_user_id' => $event->user,
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

    public function onLeadUnsubscribedFromComms(MemberUnsubscribedFromComms $event)
    {
        Member::withTrashed()->findOrFail($event->member)->update(['unsubscribed_email' => true, 'unsubscribed_sms' => true]);
    }

    public function onLeadSubscribedToComms(MemberSubscribedToComms $event)
    {
        Member::withTrashed()->findOrFail($event->member)->update(['unsubscribed_email' => false, 'unsubscribed_sms' => false]);
    }
}
