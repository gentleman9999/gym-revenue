<?php

declare(strict_types=1);

namespace App\Domain\EngagementEvents\Reactors;

use App\Domain\Agreements\Events\AgreementCreated;
use App\Domain\Agreements\Events\AgreementDeleted;
use App\Domain\Agreements\Events\AgreementSigned;
use App\Domain\Agreements\Events\AgreementUpdated;
use App\Domain\EngagementEvents\Models\EngagementEvents;
use App\Domain\Users\Events\EndUserClaimedByRep;
use App\Domain\Users\Events\EndUserConverted;
use App\Domain\Users\Events\EndUserFileUploaded;
use App\Domain\Users\Events\EndUserProfilePictureMoved;
use App\Domain\Users\Events\EndUserWasEmailedByRep;
use App\Domain\Users\Events\EndUserWasTextMessagedByRep;
use App\Domain\Users\Events\OldEndUserProfilePictureDeleted;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UserUpdated;
use App\Enums\UserTypesEnum;
use App\Support\Uuid;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EngagementEventsReactor extends Reactor
{
    public function saveMetaData(array $metaData)
    {
        $payload = [
            'id' => Uuid::new(),
            'entity' => $metaData['entity'],
            'operation' => $metaData['operation'],
            'stored-event-id' => $metaData['stored-event-id'],
            'aggregate_uuid' => $metaData['aggregate-root-uuid'],
        ];
        (new EngagementEvents())->fill($payload)->writeable()->save();
    }

    public function onUserCreated(UserCreated $event): void
    {
        if (isset($event->payload['user_type']) && $event->payload['user_type'] === UserTypesEnum::LEAD) {
            $this->saveMetaData($event->metaData());
        }
    }

    /**
     * Agreement Events
     */
    public function onAgreementSigned(AgreementSigned $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onAgreementCreated(AgreementCreated $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onAgreementDeleted(AgreementDeleted $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onAgreementUpdated(AgreementUpdated $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onLeadConverted(EndUserConverted $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onEndUserClaimedByRep(EndUserClaimedByRep $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function applyEndUserWasClaimedToRep(EndUserClaimedByRep $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onEndUserFileUploaded(EndUserFileUploaded $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onEndUserWasTextMessagedByRep(EndUserWasTextMessagedByRep $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onUserUpdated(UserUpdated $event): void
    {
        if (isset($event->payload['user_type']) && $event->payload['user_type'] === UserTypesEnum::LEAD) {
            $this->saveMetaData($event->metaData());
        }
    }

    public function onEndUserUpdated(UserUpdated $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onEndUserCreated(UserCreated $event): void
    {
        if (isset($event->payload['user_type']) && $event->payload['user_type'] === UserTypesEnum::LEAD) {
            $this->saveMetaData($event->metaData());
        }
    }

    public function onEndUserProfilePictureMoved(EndUserProfilePictureMoved $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onOldEndUserProfilePictureDeleted(OldEndUserProfilePictureDeleted $event): void
    {
        $this->saveMetaData($event->metaData());
    }

    public function onEndUserWasEmailedByRep(EndUserWasEmailedByRep $event): void
    {
        $this->saveMetaData($event->metaData());
    }
}
