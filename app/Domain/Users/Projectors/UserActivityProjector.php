<?php

namespace App\Domain\Users\Projectors;

use App\Domain\Users\Events\UserReceivedEmail;
use App\Domain\Users\Events\UserReceivedTextMsg;
use App\Domain\Users\Models\UserDetails;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserActivityProjector extends Projector
{
    public function onUserReceivedTextMsg(UserReceivedTextMsg $event)
    {
        UserDetails::create([
            'user_id' => $event->aggregateRootUuid(),
            'name' => 'sms-transmission',
            'value' => $event->template,
            'misc' => [
                'response' => $event->response,
                'client' => $event->client ?? null,
            ],
        ]);
    }

    public function onUserReceivedEmail(UserReceivedEmail $event)
    {
        UserDetails::create([
            'user_id' => $event->aggregateRootUuid(),
            'name' => 'email-transmission',
            'value' => $event->template,
            'misc' => [
                'response' => $event->response,
                'client' => $event->client ?? null,
            ],
        ]);
    }
}
