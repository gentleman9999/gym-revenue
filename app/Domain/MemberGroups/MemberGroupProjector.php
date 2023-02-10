<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups;

use App\Domain\MemberGroups\Events\MemberGroupCreated;
use App\Domain\MemberGroups\Events\MemberGroupDeleted;
use App\Domain\MemberGroups\Events\MemberGroupRestored;
use App\Domain\MemberGroups\Events\MemberGroupTrashed;
use App\Domain\MemberGroups\Events\MemberGroupUpdated;
use App\Domain\MemberGroups\Projections\MemberGroup;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class MemberGroupProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        MemberGroup::delete();
    }

    public function onMemberGroupCreated(MemberGroupCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $member_group = (new MemberGroup())->writeable();
            $member_group->fill($event->payload);
            $member_group->id = $event->aggregateRootUuid();
            $member_group->save();
        });
    }

    public function onMemberGroupDeleted(MemberGroupDeleted $event): void
    {
        MemberGroup::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onMemberGroupRestored(MemberGroupRestored $event): void
    {
        MemberGroup::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onMemberGroupTrashed(MemberGroupTrashed $event): void
    {
        MemberGroup::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onMemberGroupUpdated(MemberGroupUpdated $event): void
    {
        MemberGroup::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
