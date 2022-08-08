<?php

namespace App\Domain\Users\Projectors;

use App\Domain\Users\Events\AccessTokenGranted;
use App\Domain\Users\Events\UserReceivedEmail;
use App\Domain\Users\Events\UserReceivedTextMsg;
use App\Domain\Users\Events\UserSetCustomCrudColumns;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserDetails;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserActivityProjector extends Projector
{
    public function onUserReceivedTextMsg(UserReceivedTextMsg $event): void
    {
        UserDetails::createOrUpdateRecord($event->aggregateRootUuid(), 'sms-transmission',  $event->template, [
            'response' => $event->response,
            'client' => $event->client ?? null,
        ]);
    }

    public function onUserReceivedEmail(UserReceivedEmail $event): void
    {
        UserDetails::createOrUpdateRecord($event->aggregateRootUuid(), 'email-transmission',  $event->template, [
            'response' => $event->response,
            'client' => $event->client ?? null,
        ]);
    }

    public function onUserSetCustomCrudColumns(UserSetCustomCrudColumns $event): void
    {
        UserDetails::createOrUpdateRecord($event->aggregateRootUuid(), 'column-config',  $event->table, $event->fields);
    }

    public function onAccessTokenGranted(AccessTokenGranted $event): void
    {
        $user = User::findOrFail($event->aggregateRootUuid());
        $user->tokens()->delete();
        $token = $user->createToken($user->email)->plainTextToken;
        $user = User::findOrFail($user->id);
        $user->forceFill(['access_token' => base64_encode($token)]);
        $user->save();
    }
}
