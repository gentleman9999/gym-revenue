<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups;

use App\Domain\UserMemberGroups\Events\UserMemberGroupCreated;
use App\Domain\UserMemberGroups\Events\UserMemberGroupDeleted;
use App\Domain\UserMemberGroups\Events\UserMemberGroupUpdated;
use App\Domain\UserMemberGroups\Projections\UserMemberGroup;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserMemberGroupProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        UserMemberGroup::delete();
    }

    public function onUserMemberGroupCreated(UserMemberGroupCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $user_member_group = (new UserMemberGroup())->writeable();
            $user_member_group->fill($event->payload);
            $user_member_group->id = $event->aggregateRootUuid();
            $user_member_group->save();
        });
    }

    public function onUserMemberGroupDeleted(UserMemberGroupDeleted $event): void
    {
        UserMemberGroup::findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onUserMemberGroupUpdated(UserMemberGroupUpdated $event): void
    {
        UserMemberGroup::findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
