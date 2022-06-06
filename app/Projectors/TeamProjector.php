<?php

namespace App\Projectors;

use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\TeamDetail;
use App\StorableEvents\Teams\TeamCreated;
use App\StorableEvents\Teams\TeamDeleted;
use App\StorableEvents\Teams\TeamUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TeamProjector extends Projector
{
    public function onTeamCreated(TeamCreated $event)
    {
        //get only the keys we care about (the ones marked as fillable)
        $team_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Team())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $team_owner = $event->user;
        $client_id = $event->payload['client_id'] ?? null;
        if (! $team_owner && $client_id) {
            $team_owner = Client::findOrFail($client_id)->accountOwners[0]->id ?? 1;
        }
        if (! array_key_exists('user_id', $team_table_data) && $team_owner) {
            $team_table_data['user_id'] = $team_owner;
        }
        $team = Team::create($team_table_data);
        foreach ($event->payload['locations'] ?? [] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $event->aggregateRootUuid(), 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }

    public function onTeamDeleted(TeamDeleted $event)
    {
        Team::findOrFail($event->id)->deleteOrFail();
    }

    public function onTeamUpdated(TeamUpdated $event)
    {
        $team_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Team())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        Team::findOrFail($event->aggregateRootUuid())->update($team_table_data);
        TeamDetail::whereTeamId($event->aggregateRootUuid())->whereName('team-location')->delete();
        foreach ($event->payload['locations'] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $event->aggregateRootUuid(), 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }
}
