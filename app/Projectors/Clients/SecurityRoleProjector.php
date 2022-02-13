<?php

namespace App\Projectors\Clients;

use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleCreated;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleRestored;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleTrashed;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Bouncer;

class SecurityRoleProjector extends Projector
{
    public function onSecurityRoleCreated(SecurityRoleCreated $event)
    {
        SecurityRole::create(
            $event->payload
        );
    }

    public function onSecurityRoleUpdated(SecurityRoleUpdated $event)
    {
        $securityRole = SecurityRole::findOrFail($event->payload['id'])->updateOrFail($event->payload);
//        $securityRole->updateOrFail($event->payload);
    }

    public function onSecurityRoleTrashed(SecurityRoleTrashed $event)
    {
        SecurityRole::findOrFail($event->id)->delete();
    }

    public function onSecurityRoleRestored(SecurityRoleRestored $event)
    {
        SecurityRole::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onSecurityRoleDeleted(SecurityRoleDeleted $event)
    {
        SecurityRole::withTrashed()->findOrFail($event->id)->forceDelete();
    }
}
