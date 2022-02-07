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
        $securityRole = SecurityRole::create(
            $event->payload
        );

        $client = Client::with('teams')->findOrFail($event->client);
        $clientTeamIds = $client->teams->pluck('value');
        collect($securityRole->ability_ids)->each(function ($ability_id) use ($clientTeamIds, $securityRole) {
            $clientTeamIds->each(function ($clientTeamId) use ($ability_id, $securityRole) {
                $ability = Bouncer::ability()->find($ability_id);
                $team = Team::find($clientTeamId);
                if ($ability && $team) {
                    Bouncer::allow($securityRole->security_role)->to($ability->name, $team);
                }
            });
        });
    }

    public function onSecurityRoleUpdated(SecurityRoleUpdated $event)
    {
        $securityRole = SecurityRole::findOrFail($event->payload['id']);
        $securityRole->updateOrFail($event->payload);

        $client = Client::with('teams')->findOrFail($event->client);
        $clientTeamIds = $client->teams->pluck('value');
        Bouncer::sync($securityRole->security_role)->abilities([]);
        collect($securityRole->ability_ids)->each(function ($ability_id) use ($clientTeamIds, $securityRole) {
            $clientTeamIds->each(function ($clientTeamId) use ($ability_id, $securityRole) {
                $ability = Bouncer::ability()->find($ability_id);
                $team = Team::find($clientTeamId);
                if ($ability && $team) {
                    Bouncer::allow($securityRole->security_role)->to($ability->name, $team);
                }
            });
        });
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
