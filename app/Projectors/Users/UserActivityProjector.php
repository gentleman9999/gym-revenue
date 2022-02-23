<?php

namespace App\Projectors\Users;

use App\Models\UserDetails;
use App\StorableEvents\Users\Activity\Email\UserReceivedEmail;
use App\StorableEvents\Users\Activity\SMS\UserReceivedTextMsg;
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
                'client' => $event->client ?? null
            ]
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
                'client' => $event->client ?? null
            ]
        ]);
    }
}
