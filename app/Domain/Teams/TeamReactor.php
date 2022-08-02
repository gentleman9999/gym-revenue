<?php

namespace App\Domain\Teams;

use App\Domain\Teams\Events\TeamMemberInvited;
use App\Domain\Teams\Models\Team;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Mail\TeamInvitation;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class TeamReactor extends Reactor
{
    //this is untested, just converted over in case we end up using.
    public function onTeamMemberInvited(TeamMemberInvited $event): void
    {
        $team = Team::findOrFail($event->aggregateRootUuid());
        $invitation = $team->teamInvitations()->whereEmail($event->email)->firstOrFail();
        Mail::to($event->email)->send(new TeamInvitation($invitation));
    }
}
