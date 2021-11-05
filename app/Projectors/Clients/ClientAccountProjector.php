<?php

namespace App\Projectors\Clients;

use App\Actions\Jetstream\AddTeamMember;
use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\ClientDetail;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\DefaultClientTeamCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientAccountProjector extends Projector
{
    public function onDefaultClientTeamCreated(DefaultClientTeamCreated $event)
    {
        $default_team_name = $event->team;
        $team = Team::create([
            'user_id' => 1,
            'name' => $default_team_name,
            'personal_team' => 0
        ]);
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'default-team',
            'value' => $default_team_name
        ]);
        ClientDetail::create([
            'client_id' => $event->client,
            'detail' => 'team',
            'value' => $team->id
        ]);

        ClientAggregate::retrieve($event->client)
            ->addTeam($team->id, $default_team_name)
            ->addCapeAndBayAdminsToTeam($team->id)
            ->persist();
    }

    public function onCapeAndBayUsersAssociatedWithClientsNewDefaultTeam(CapeAndBayUsersAssociatedWithClientsNewDefaultTeam $event)
    {
        $users = User::whereIn('id', $event->payload)->get();
        $team = Team::find($event->team);

        foreach($users as $newTeamMember)
        {
            $team->users()->attach(
                $newTeamMember, ['role' => 'Admin']
            );
        }
    }
}
