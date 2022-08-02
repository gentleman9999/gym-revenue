<?php

namespace App\Domain\EndUsers\Projectors;

use App\Domain\EndUsers\Events\EndUserCreated;
use App\Domain\EndUsers\Events\EndUserDeleted;
use App\Domain\EndUsers\Events\EndUserProfilePictureMoved;
use App\Domain\EndUsers\Events\EndUserRestored;
use App\Domain\EndUsers\Events\EndUserTrashed;
use App\Domain\EndUsers\Events\EndUserUpdated;
use App\Domain\EndUsers\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Projections\EndUser;
use App\Models\Note;

abstract class EndUserCrudProjector extends BaseEndUserProjector
{
    public function onEndUserCreated(EndUserCreated $event): void
    {
        $end_user = $this->getModel()->writeable();
        if ($end_user::class !== $event->getEntity()) {
            return;
        }
        //get only the keys we care about (the ones marked as fillable)
        $fillable_data = array_filter($event->payload, function ($key) use ($end_user) {
            return in_array($key, $end_user->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $end_user->id = $event->aggregateRootUuid();
        $end_user->client_id = $event->payload['client_id'];
        $end_user->email = $event->payload['email'];
        if (array_key_exists('agreement_number', $event->payload)) {
            $end_user->agreement_number = $event->payload['agreement_number'];
        }
        $end_user->fill($fillable_data);

        $end_user->save();

        ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'creates', $event->modifiedBy());
        ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'created', $event->createdAt());

        $this->createOrUpdateEndUserDetailsAndNotes($event, $end_user);
    }

    public function onEndUserUpdated(EndUserUpdated $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = $this->getModel()::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();

        if (array_key_exists('email', $event->payload)) {
            $end_user->email = $event->payload['email'];
        }
        $end_user->fill($event->payload);
        $end_user->save();

        ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'updated', $event->modifiedBy());

        $this->createOrUpdateEndUserDetailsAndNotes($event, $end_user);
    }

    public function onEndUserTrashed(EndUserTrashed $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = $this->getModel()::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
        $end_user->deleteOrFail();

        ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'softdelete', $event->reason, ['userid' => $event->modifiedBy()]);
    }

    public function onEndUserRestored(EndUserRestored $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $this->getModel()::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onEndUserDeleted(EndUserDeleted $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $this->getModel()::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onEndUserProfilePictureMoved(EndUserProfilePictureMoved $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $this->getModel()::findOrFail($event->aggregateRootUuid())->writeable()->update(['profile_picture' => $event->file]);
    }

    public function onEndUserUpdatedCommunicationPreferences(EndUserUpdatedCommunicationPreferences $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = $this->getModel()::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
        $end_user->forceFill(['unsubscribed_email' => $event->email, 'unsubscribed_sms' => $event->sms])->save();
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

            ($end_user::getDetailsModel())->createOrUpdateRecord($end_user->id, 'note_created',  $notes['note']);
        }
    }
}
