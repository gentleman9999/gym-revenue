<?php

namespace App\Domain\Teams;

use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Events\TeamDeleted;
use App\Domain\Teams\Events\TeamMemberAdded;
use App\Domain\Teams\Events\TeamMemberInvited;
use App\Domain\Teams\Events\TeamMemberRemoved;
use App\Domain\Teams\Events\TeamUpdated;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamInvitation;
use App\Domain\Teams\Models\TeamUser;
use App\Domain\Users\Models\User;
use Laravel\Jetstream\Jetstream;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TeamProjector extends Projector
{
    public function onStartingEventReplay()
    {
        Team::truncate();
        TeamInvitation::truncate();
        TeamUser::truncate();
    }

    public function onTeamCreated(TeamCreated $event): void
    {
        $team = new Team();
        //get only the keys we care about (the ones marked as fillable)
        $team_fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Team())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $team->fill($team_fillable_data);
        $team->id = $event->aggregateRootUuid();
        $team->client_id = $event->payload['client_id'] ?? null;
        //TODO:just use a team_location pivot table
        $team->details = ['team-locations' => $event->payload['locations'] ?? []];
        $team->save();
    }

    public function onTeamDeleted(TeamDeleted $event): void
    {
        Team::findOrFail($event->aggregateRootUuid())->deleteOrFail();
    }

    public function onTeamUpdated(TeamUpdated $event): void
    {
        $team_fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Team())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $team = Team::findOrFail($event->aggregateRootUuid())->updateOrFail($team_fillable_data);

        if (array_key_exists('locations', $event->payload)) {
            $team->details = ['team-locations' => $event->payload['locations']];
            $team->save();
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

//    public function onCapeAndBayUsersAssociatedWithClientsNewDefaultTeam(CapeAndBayUsersAssociatedWithClientsNewDefaultTeam $event)
//    {
//        $users = User::whereIn('id', $event->payload)->get();
//        $team = Team::find($event->team);
//
//        foreach ($users as $newTeamMember) {
//            $team->users()->attach($newTeamMember);
//            $team_client = Team::getClientFromTeamId($team->id);
//            $team_client_id = ($team_client) ? $team_client->id : null;
//
//            // Since the user needs to have their team added in a single transaction in createUser
//            // A projector won't get executed (for now) but an apply function will run on the next retrieval
//            UserAggregate::retrieve($newTeamMember->id)
//                ->addUserToTeam($team->id, $team->name, $team_client_id)
//                ->persist();
//        }
//    }
}
