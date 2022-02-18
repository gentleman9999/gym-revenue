<?php

namespace App\Aggregates\Users;

use App\StorableEvents\Users\UserCreated;
use App\StorableEvents\Users\UserDeleted;
use App\StorableEvents\Users\UserUpdated;
use App\StorableEvents\Users\WelcomeEmailSent;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot
{
    protected $client_id = '';

    public function applyNewUser(UserCreated $event)
    {
        // @todo - put something useful here
    }

    public function createUser(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new UserCreated($this->uuid(), $created_by_user_id, $payload));
        return $this;
    }

    public function deleteUser(string $deleted_by_user_id, array $payload)
    {
        $this->recordThat(new UserDeleted($this->uuid(), $deleted_by_user_id, $payload));
        return $this;
    }

    public function updateUser(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new UserUpdated($this->uuid(), $updated_by_user_id, $payload));
        return $this;
    }

    public function sendWelcomeEmail()
    {
        // @todo - logic to throw an exception if the user is active
        $this->recordThat(new WelcomeEmailSent($this->uuid()));
        return $this;
    }
}
