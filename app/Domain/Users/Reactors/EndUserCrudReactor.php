<?php

namespace App\Domain\Users\Reactors;

use App\Actions\Mail\MailgunSend;
use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Events\EndUserProfilePictureMoved;
use App\Domain\Users\Events\EndUserWasEmailedByRep;
use App\Domain\Users\Events\EndUserWasTextMessagedByRep;
use App\Domain\Users\Events\OldEndUserProfilePictureDeleted;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UserUpdated;
use App\Domain\Users\Models\EndUser;
use Illuminate\Support\Facades\Storage;

class EndUserCrudReactor extends BaseEndUserReactor
{
    public function onEndUserUpdated(UserUpdated $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $aggy = ($this->getAggregate())::retrieve($event->aggregateRootUuid());
        $oldData = $aggy->getOldData();
        $this->maybeMoveProfilePicture($event->aggregateRootUuid(), $event->clientId(), $event->payload, $oldData);
    }

    public function onEndUserCreated(UserCreated $event): void
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
        if (! $event->old_file) {
            return;
        }
        ($this->getAggregate())::retrieve($event->aggregateRootUuid())->deleteOldProfilePicture($event->old_file)->persist();
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
        if (isset($data['profile_picture'])) {
            return;
        }
        $file = $data['profile_picture'];
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

    public function onEndUserWasTextMessagedByRep(EndUserWasTextMessagedByRep $event): void
    {
        $end_user = $this->getModel()::findOrFail($event->aggregateRootUuid());
        $misc = $event->payload;
        FireTwilioMsg::run($end_user, $misc['message']);
    }

    public function onEndUserWasEmailedByRep(EndUserWasEmailedByRep $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = $this->getModel()::findOrFail($event->aggregateRootUuid())->writeable();
        $misc = $event->payload;
        MailgunSend::run([$end_user->email], $misc['subject'], $misc['message']);
    }

    public static function getModel(): EndUser
    {
        return new EndUser();
    }

    public static function getAggregate(): UserAggregate
    {
        return new UserAggregate();
    }
}
