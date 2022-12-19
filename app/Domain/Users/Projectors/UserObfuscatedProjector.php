<?php

declare(strict_types=1);

namespace App\Domain\Users\Projectors;

use App\Domain\Users\Events\UserObfuscated;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserDetails;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserObfuscatedProjector extends Projector
{
    public function onStartingEventReplay()
    {
        User::query()->delete();
        UserDetails::query()->delete();
    }

    public function onUserObfuscated(UserObfuscated $event): void
    {
        $user = User::find($event->aggregateRootUuid());
        $timestamp = $event->createdAt()->toDate()->getTimestamp();
        $user->obfuscated_at = date("Y-m-d H:i:s", $timestamp);
        $user->save();
    }
}
