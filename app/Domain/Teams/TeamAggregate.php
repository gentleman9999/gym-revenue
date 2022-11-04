<?php

namespace App\Domain\Teams;

use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Events\TeamDeleted;
use App\Domain\Teams\Events\TeamMemberAdded;
use App\Domain\Teams\Events\TeamMemberInvited;
use App\Domain\Teams\Events\TeamMemberRemoved;
use App\Domain\Teams\Events\TeamRestored;
use App\Domain\Teams\Events\TeamTrashed;
use App\Domain\Teams\Events\TeamUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class TeamAggregate extends AggregateRoot
{
    public $members = [];

    public function create(array $payload): static
    {
        $this->recordThat(new TeamCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new TeamTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new TeamRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new TeamDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new TeamUpdated($payload));

        return $this;
    }

    public function inviteMember(string $email): static
    {
        $this->recordThat(new TeamMemberInvited($email));

        return $this;
    }

    //TODO: should be id not email
    public function addMember(string $email): static
    {
        $this->recordThat(new TeamMemberAdded($email));

        return $this;
    }

    //TODO: should be id not email
    public function removeMember(string $email): static
    {
        $this->recordThat(new TeamMemberRemoved($email));

        return $this;
    }
//
//    public function addCapeAndBayAdminsToTeam(string $team_id)
//    {
//        $team = Team::find($team_id);
//        $users = Team::whereName('Cape & Bay Admin Team')->first()->users;
//        if (count($users) > 0) {
//            $payload = [];
//            foreach ($users as $user) {
//                $payload[] = $user->id;
//                UserAggregate::retrieve($user->id)
//                    ->addUserToTeam($team_id, $team->name, $this->uuid())
//                    ->persist();
//            }
//
//            $this->recordThat(new CapeAndBayUsersAssociatedWithClientsNewDefaultTeam($this->uuid(), $team_id, $payload));
//        } else {
//            throw ClientAccountException::noCapeAndBayUsersAssigned();
//        }
//
//        return $this;
//    }
}
