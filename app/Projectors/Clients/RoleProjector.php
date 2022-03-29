<?php

namespace App\Projectors\Clients;

use App\StorableEvents\Clients\Roles\RoleCreated;
use App\StorableEvents\Clients\Roles\RoleDeleted;
use App\StorableEvents\Clients\Roles\RoleRestored;
use App\StorableEvents\Clients\Roles\RoleTrashed;
use App\StorableEvents\Clients\Roles\RoleUpdated;
use App\Models\Role;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Silber\Bouncer\BouncerFacade as Bouncer;

class RoleProjector extends Projector
{
    public function onRoleCreated(RoleCreated $event)
    {
        Role::create(
            $event->payload,
        );
        foreach ($event->payload['ability_names'] as $ability)
        {
            Bouncer::allow($event->payload['name'])->to($ability, \App\Models\Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
        }
    }

    public function onRoleUpdated(RoleUpdated $event)
    {
        Bouncer::disallow($event->payload['name'])->everything();
        foreach ($event->payload['ability_names'] as $ability)
        {
            Bouncer::allow($event->payload['name'])->to($ability, \App\Models\Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
        }
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
