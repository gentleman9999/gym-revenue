<?php

namespace App\Domain\EndUsers\Projectors;

use App\Actions\Mail\MailgunSend;
use App\Domain\EndUsers\Events\EndUserClaimedByRep;
use App\Domain\EndUsers\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Events\EndUserWasCalledByRep;
use App\Domain\EndUsers\Events\EndUserWasEmailedByRep;
use App\Domain\EndUsers\Events\EndUserWasTextMessagedByRep;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Users\Models\User;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EndUserActivityProjector extends Projector
{
    public function onEndUserClaimedByRep(EndUserClaimedByRep $event): void
    {
        DB::transaction(function () use ($event) {
            $end_user = EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
            $end_user->owner_user_id = $event->claimedByUserId;
            $end_user->save();

            $end_user = Lead::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
            $end_user->owner_user_id = $event->claimedByUserId;
            $end_user->save();
        });
    }

    public function onEndUserWasEmailedByRep(EndUserWasEmailedByRep $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = $this->getModel()::findOrFail($event->aggregateRootUuid())->writeable();
        $user = User::find($event->userId());

        $misc = $event->payload;
        $misc['user_email'] = $user->email;

        //$mailgunResponse = MailgunSend::run([$end_user->email], $misc['subject'], $misc['message']);

        $emailed = ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'emailed_by_rep', $event->modifiedBy(), $misc);
    }

    public function onEndUserWasTextMessagedByRep(EndUserWasTextMessagedByRep $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = $this->getModel()::findOrFail($event->aggregateRootUuid())->writeable();

        $misc = $event->payload;

        ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'sms_by_rep', $event->modifiedBy(), $misc);
    }

    public function onEndUserWasCalledByRep(EndUserWasCalledByRep $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = $this->getModel()::findOrFail($event->aggregateRootUuid())->writeable();
        $user = User::find($event->modifiedBy());

        $misc = $event->payload;
        $misc['user_email'] = $user->email;

        ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'called_by_rep', $event->modifiedBy(), $misc);

        $notes = $misc['notes'] ?? false;
        if ($notes) {
            // TODO: use action
            Note::create([
                'entity_id' => $end_user->id,
                'entity_type' => ($end_user::getDetailsModel())::class,
                'note' => $notes,
                'title' => 'Call Notes',
                'created_by_user_id' => $event->modifiedBy(),
            ]);
        }
    }

    protected function createOrUpdateEndUserDetailsAndNotes($event, EndUser $end_user): void
    {
        foreach ($this->getModel()->getDetailFields() as $field) {
            ($end_user::getDetailsModel())::createOrUpdateRecord($event->aggregateRootUuid(), $field, $event->payload[$field] ?? null);
        }

        $notes = $event->payload['notes'] ?? false;
        if ($notes && $notes['title'] ?? false) {
            Note::create([
                'entity_id' => $end_user->id,
                'entity_type' => ($end_user::getDetailsModel())::class,
                'title' => $notes['title'],
                'note' => $notes['note'],
                'created_by_user_id' => $event->modifiedBy(),
            ]);

            $noted_created = ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'note_created',  $notes['note']);
        }
    }

    public function onEndUserUpdatedCommunicationPreferences(EndUserUpdatedCommunicationPreferences $event): void
    {
        $end_user = $this->getModel()::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
        $end_user->forceFill(['unsubscribed_email' => $event->email, 'unsubscribed_sms' => $event->sms])->save();
    }
}
