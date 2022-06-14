<?php

namespace App\Domain\Users\Projectors;

use App\Domain\Users\Events\UserReceivedEmail;
use App\Domain\Users\Events\UserReceivedTextMsg;
use App\Models\UserDetails;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserActivityProjector extends Projector
{
    public function onUserReceivedTextMsg(UserReceivedTextMsg $event)
    {
        UserDetails::create([
            'user_id' => $event->user,
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
            'user_id' => $event->user,
            'name' => 'email-transmission',
            'value' => $event->template,
            'misc' => [
                'response' => $event->response,
                'client' => $event->client ?? null,
            ],
        ]);
    }
}
