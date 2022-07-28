<?php

namespace App\Domain\EndUsers\Reactors;

use App\Domain\EndUsers\Events\EndUserCreated;
use App\Domain\EndUsers\Events\EndUserProfilePictureMoved;
use App\Domain\EndUsers\Events\EndUserUpdated;
use App\Domain\EndUsers\Events\OldEndUserProfilePictureDeleted;
use Illuminate\Support\Facades\Storage;

abstract class EndUserCrudReactor extends BaseEndUserReactor
{
    public function onEndUserUpdated(EndUserUpdated $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $aggy = ($this->getAggregate())::retrieve($event->aggregateRootUuid());
        $oldData = $aggy->getOldData();
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->clientId(), $event->payload, $oldData);
    }

    public function onEndUserCreated(EndUserCreated $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->clientId(), $event->payload);
    }

    public function onEndUserProfilePictureMoved(EndUserProfilePictureMoved $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        if (! $event->oldFile) {
            return;
        }
        ($this->getAggregate())::retrieve($event->aggregateRootUuid())->deleteOldProfilePicture($event->oldFile)->persist();
    }

    public function onOldEndUserProfilePictureDeleted(OldEndUserProfilePictureDeleted $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        Storage::disk('s3')->delete($event->file['key']);
    }

    protected function maybeMoveProfilePicture(string $lead_id, string $client_id, array $data, array $oldData = null): void
    {
        $file = $data['profile_picture'] ?? false;

        if (! $file) {
            return;
        }
        $destKey = "{$client_id}/{$file['uuid']}";
        Storage::disk('s3')->move($file['key'], $destKey);
        $file['key'] = $destKey;
        $file['url'] = Storage::disk('s3')->url($file['key']);
        $aggy = ($this->getAggregate())::retrieve($lead_id);
        if ($oldData['profile_picture']['misc'] ?? false) {
//            $aggy->moveProfilePicture($file, $oldData['profile_picture']['misc']);
            $aggy->moveProfilePicture($file, $oldData['profile_picture']);
        } else {
            $aggy->moveProfilePicture($file);
        }
        $aggy->persist();
    }
}
