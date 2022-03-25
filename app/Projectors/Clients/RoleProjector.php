<?php

namespace App\Projectors\Clients;

use App\Models\Clients\Classification;
use App\StorableEvents\Clients\Roles\RoleCreated;
use App\StorableEvents\Clients\Roles\RoleDeleted;
use App\StorableEvents\Clients\Roles\RoleRestored;
use App\StorableEvents\Clients\Roles\RoleTrashed;
use App\StorableEvents\Clients\Roles\RoleUpdated;
use Bouncer;
use Silber\Bouncer\Database\Role;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RoleProjector extends Projector
{
    public function onRoleCreated(RoleCreated $event)
    {
        Role::create(
            $event->payload
        );
    }

    public function onRoleUpdated(RoleUpdated $event)
    {
        Role::findOrFail($event->payload['id'])->updateOrFail($event->payload);
    }

    public function onRoleTrashed(RoleTrashed $event)
    {
        Role::findOrFail($event->id)->delete();
    }

    public function onRoleRestored(RoleRestored $event)
    {
        Role::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onRoleDeleted(RoleDeleted $event)
    {
        Role::withTrashed()->findOrFail($event->id)->forceDelete();
    }
}
