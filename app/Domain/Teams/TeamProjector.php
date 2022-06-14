<?php

namespace App\Domain\Teams;

use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Events\TeamDeleted;
use App\Domain\Teams\Events\TeamMemberAdded;
use App\Domain\Teams\Events\TeamMemberInvited;
use App\Domain\Teams\Events\TeamMemberRemoved;
use App\Domain\Teams\Events\TeamUpdated;
use App\Domain\Teams\Models\Team;
use App\Models\TeamDetail;
use Laravel\Jetstream\Jetstream;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TeamProjector extends Projector
{
    public function onTeamCreated(TeamCreated $event): void
    {
        $team = new Team();
        $team->fill($event->payload);
        $team->id = $event->aggregateRootUuid();
        $team->client_id = $event->payload['client_id'] ?? null;
        $team->save();
        //TODO: make sure creating clients sets them up a home team;
//        if ($team->home_team) {
//            $team->client->home_team_id = $team->id;
//            $team->save();
//        }
        //TODO:just use a team_location pivot table
        foreach ($event->payload['locations'] ?? [] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $event->aggregateRootUuid(), 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }

    public function onTeamDeleted(TeamDeleted $event): void
    {
        Team::findOrFail($event->aggregateRootUuid())->deleteOrFail();
    }

    public function onTeamUpdated(TeamUpdated $event): void
    {
        Team::findOrFail($event->aggregateRootUuid())->updateOrFail($event->payload);
        TeamDetail::whereTeamId($event->aggregateRootUuid())->whereName('team-location')->delete();
        foreach ($event->payload['locations'] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $event->aggregateRootUuid(), 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
    }

    public function onTeamMemberInvited(TeamMemberInvited $event): void
    {
        $team = Team::findOrFail($event->aggregateRootUuid());

        $team->teamInvitations()->create([
            'email' => $event->email,
//            'role' => $role,
        ]);
    }

    public function onTeamMemberAdded(TeamMemberAdded $event): void
    {
        $newTeamMember = Jetstream::findUserByEmailOrFail($event->email);

        Team::findOrFail($event->aggregateRootUuid())->users()->attach(
            $newTeamMember,
//            ['role' => $role]
        );
    }

    public function onTeamMemberRemoved(TeamMemberRemoved $event): void
    {
        Team::findOrFail($event->aggregateRootUuid())->removeUser(User::findOrFail($event->id));
    }
}
