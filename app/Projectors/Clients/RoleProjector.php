<?php

namespace App\Projectors\Clients;

use App\Models\Role;
use App\StorableEvents\Clients\Roles\RoleCreated;
use App\StorableEvents\Clients\Roles\RoleDeleted;
use App\StorableEvents\Clients\Roles\RoleRestored;
use App\StorableEvents\Clients\Roles\RoleTrashed;
use App\StorableEvents\Clients\Roles\RoleUpdated;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RoleProjector extends Projector
{
    public function onRoleCreated(RoleCreated $event)
    {
        $role = Role::create(
            [
                'id' => $event->payload['id'],
                'name' => $event->payload['name'],
                'title' => $event->payload['name'],
                'scope' => $event->payload['client_id'],
                'group' => $event->payload['group'],
            ]
        );
        foreach ($event->payload['ability_names'] as $ability) {
            Bouncer::allow($role->name)->to($ability, \App\Models\Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
        }
    }

    public function onRoleUpdated(RoleUpdated $event)
    {
        $role = Bouncer::role()->findOrFail($event->payload['id']);
        Bouncer::sync($role)->abilities([]);
        foreach ($event->payload['ability_names'] as $ability) {
            Bouncer::allow($role->name)->to($ability, \App\Models\Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
        }
        Role::findOrFail($event->payload['id'])->updateOrFail(array_merge($event->payload, ['title' => $event->payload['name']]));
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
