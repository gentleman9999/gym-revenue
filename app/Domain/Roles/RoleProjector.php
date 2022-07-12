<?php

namespace App\Domain\Roles;

use App\Domain\Roles\Events\RoleCreated;
use App\Domain\Roles\Events\RoleDeleted;
use App\Domain\Roles\Events\RoleUpdated;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RoleProjector extends Projector
{
    public function onRoleCreated(RoleCreated $event)
    {
        $role = new Role();
        //get only the keys we care about (the ones marked as fillable)
        $role_fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Role())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $role->fill($role_fillable_data);
        $role->id = $event->aggregateRootUuid();
        $role->save();
        foreach ($event->payload['ability_names'] as $ability) {
            Bouncer::allow($role->name)->to($ability, Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
        }
    }

    public function onRoleUpdated(RoleUpdated $event)
    {
        $role = Bouncer::role()->findOrFail($event->aggregateRootUuid());
        Bouncer::sync($role)->abilities([]);
        foreach ($event->payload['ability_names'] as $ability) {
            Bouncer::allow($role->name)->to($ability, Role::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
        }
        $data = $event->payload;
        if (array_key_exists('name', $event->payload)) {
            $data['title'] = $event->payload['name'];
        }
        Role::findOrFail($event->aggregateRootUuid())->updateOrFail($data);
    }

    public function onRoleDeleted(RoleDeleted $event)
    {
        Role::findOrFail($event->aggregateRootUuid())->deleteOrFail();
    }
}
