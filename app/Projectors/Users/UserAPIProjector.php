<?php

namespace App\Projectors\Users;

use App\Models\User;
use App\StorableEvents\Users\Activity\API\AccessTokenGranted;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserAPIProjector extends Projector
{
    public function onAccessTokenGranted(AccessTokenGranted $event)
    {
        $user = User::find($event->user);
        $user->tokens()->delete();
        $token = $user->createToken($user->email)->plainTextToken;
        $user = User::findOrFail($user->id);
        $user->updateOrFail([
            'user_id' => $user->id,
            'access_token' => base64_encode($token),
        ]);
    }
}
