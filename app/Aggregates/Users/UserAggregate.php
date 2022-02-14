<?php

namespace App\Aggregates\Users;

use App\StorableEvents\Users\NewUserCreated;
use App\StorableEvents\Users\PresetAssociatedClient;
use App\StorableEvents\Users\SecurityRoleAssigned;
use App\StorableEvents\Users\SecurityRolePreassigned;
use App\StorableEvents\Users\SetAssociatedClient;
use App\StorableEvents\Users\WelcomeEmailSent;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot
{
    protected $client_id = '';

    public function applyNewUser(NewUserCreated $event)
    {
        // @todo - put something useful here
    }
    public function applySecurityRoleAssigned(SecurityRoleAssigned $event)
    {
        // @todo - put something useful here
    }
    public function applySecurityRolePreassigned(SecurityRolePreassigned $event)
    {

    }

    public function applySetAssociatedClient(SetAssociatedClient $event)
    {
        $this->client_id = $event->data;
    }

    public function applyPresetAssociatedClient(PresetAssociatedClient $event)
    {
        $this->client_id = $event->data;
    }

    public function createNewUser(string $creating_user)
    {
        $this->recordThat(new NewUserCreated($this->uuid(), $creating_user));
        return $this;
    }

    public function imGonnaGoAheadAndAssignThisSecurityRole($security_role_id)
    {
        $this->recordThat(new SecurityRolePreassigned($this->uuid(), $security_role_id));
        return $this;
    }

    public function assignSecurityRole($security_role_id)
    {
        $this->recordThat(new SecurityRoleAssigned($this->uuid(), $security_role_id));
        return $this;
    }

    public function imGonnaGoAheadAndAssignThisClient(string $client_id)
    {
        $this->recordThat(new PresetAssociatedClient($this->uuid(), $client_id));
        return $this;
    }

    public function assignAsociatedClient(string $client_id)
    {
        $this->recordThat(new SetAssociatedClient($this->uuid(), $client_id));
        return $this;
    }

    public function sendWelcomeEmail()
    {
        // @todo - logic to throw an exception if the user is active
        $this->recordThat(new ($this->uuid()));
        return $this;
    }
}
