<?php

declare(strict_types=1);

namespace App\Domain\Users\Projectors;

use App\Domain\Users\Events\UserObfuscated;
use App\Domain\Users\Models\ObfuscatedUser;
use App\Domain\Users\Models\User;
use App\Domain\Users\Services\UserDataReflector;
use App\Support\Uuid;
use Illuminate\Support\Facades\Hash;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserObfuscatedProjector extends Projector
{
    public function onUserObfuscated(UserObfuscated $event): void
    {
        $user = User::find($event->aggregateRootUuid());
        $timestamp = $event->createdAt()->toDate()->getTimestamp();
        $user->obfuscated_at = date("Y-m-d H:i:s", $timestamp);
        $user->save();
        UserDataReflector::reflectData($user);
        $obfuscated_user = new ObfuscatedUser();
        $obfuscated_user->id = Uuid::new();
        $obfuscated_user->client_id = $user->client_id;
        $obfuscated_user->user_id = $user->id;
        $obfuscated_user->email = $user->email;
        $obfuscated_user->data = [
            'first_name' => Hash::make($user->first_name),
            'last_name' => Hash::make($user->last_name),
            'phone' => Hash::make($user->phone),
            'email' => Hash::make($user->email),
            'address1' => Hash::make($user->address1),
            'address2' => Hash::make($user->address2),
            ];
        $obfuscated_user->writeable()->save();
    }
}
