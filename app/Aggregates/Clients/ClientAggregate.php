<?php

namespace App\Aggregates\Clients;

use App\Exceptions\Clients\ClientAccountException;
use App\Models\UserDetails;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\DefaultClientTeamCreated;
use App\StorableEvents\Clients\TeamCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientAggregate extends AggregateRoot
{
    protected string $default_team = '';
    protected array $teams = [];

    protected static bool $allowConcurrency = true;

    public function applyDefaultClientTeamCreated(DefaultClientTeamCreated $event)
    {
        $this->default_team = $event->team;
    }

    public function applyTeamCreated(TeamCreated $event)
    {
        $this->teams[$event->team] = $event->name;
    }

    public function createDefaultTeam(string $name)
    {
        if(!empty($this->default_team))
        {
            throw ClientAccountException::defaultTeamAlreadyCreated($this->default_team);
        }
        else
        {
            $this->recordThat(new DefaultClientTeamCreated($this->uuid(), $name));
        }

        return $this;
    }

    public function createNewEmailTemplate(string $template_id, string $created_by = null)
    {
        $this->recordThat(new EmailTemplateCreated($this->uuid(), $template_id, $created_by));
        return $this;
    }

    public function createNewSMSTemplate(string $template_id, string $created_by = null)
    {
        $this->recordThat(new SMSTemplateCreated($this->uuid(), $template_id, $created_by));
        return $this;
    }

    public function addTeam(string $team_id, string $team_name)
    {
        if(array_key_exists($team_id, $this->teams))
        {
            throw ClientAccountException::teamAlreadyAssigned($team_name);
        }
        else {
            // @todo - make sure the team is not assigned to another client
            $this->recordThat(new TeamCreated($this->uuid(), $team_id, $team_name));
        }
        return $this;
    }

    public function addCapeAndBayAdminsToTeam(string $team_id)
    {
        $users = UserDetails::select('user_id')
            ->whereName('default_team')
            ->whereValue(1)->get();

        if(count($users) > 0)
        {
            $payload = [];
            foreach($users as $user)
            {
                $payload[] = $user->user_id;
            }

            $this->recordThat(new CapeAndBayUsersAssociatedWithClientsNewDefaultTeam($this->uuid(), $team_id, $payload));
        }
        else
        {
            throw ClientAccountException::noCapeAndBayUsersAssigned();
        }

        return $this;
    }
}
