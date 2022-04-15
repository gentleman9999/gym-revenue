<?php

namespace App\Aggregates\Clients\Traits\Actions;


use App\StorableEvents\Clients\Reminder\ReminderCreated;

trait ClientReminderActions
{

    public function createReminder(string $user_id, array $payload)
    {
        $this->recordThat(new ReminderCreated($this->uuid(), $user_id, $payload));
        return $this;
    }

}
