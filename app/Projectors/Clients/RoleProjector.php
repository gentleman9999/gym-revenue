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
        $role = Role::create(
            array_merge( $event->payload, ['name' => "$event->client {$event->payload['title']}"])
        );
        foreach ($event->payload['ability_names'] as $ability)
        {
            Bouncer::allow($role->name)->to($ability, \App\Models\Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
        }
    }

    public function onRoleUpdated(RoleUpdated $event)
    {
        $role = Bouncer::role()->findOrFail($event->payload['id']);
        Bouncer::disallow($role->name)->everything();
        foreach ($event->payload['ability_names'] as $ability)
        {
            Bouncer::allow($role->name)->to($ability, \App\Models\Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
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
        Role::findOrFail($event->id)->delete();
    }
}
