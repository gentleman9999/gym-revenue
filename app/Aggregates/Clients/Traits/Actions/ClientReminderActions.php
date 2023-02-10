<?php

declare(strict_types=1);

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Reminders\ReminderCreated;
use App\StorableEvents\Clients\Reminders\ReminderDeleted;
use App\StorableEvents\Clients\Reminders\ReminderRestored;
use App\StorableEvents\Clients\Reminders\ReminderTrashed;
use App\StorableEvents\Clients\Reminders\ReminderUpdated;

trait ClientReminderActions
{
    public function createReminder(string $user_id, array $payload)
    {
        $this->recordThat(new ReminderCreated($this->uuid(), $user_id, $payload));

        return $this;
    }

    public function updateReminder(string $user_id, array $payload)
    {
        $this->recordThat(new ReminderUpdated($this->uuid(), $user_id, $payload));

        return $this;
    }

    public function trashReminder(string $user_id, string $id)
    {
        $this->recordThat(new ReminderTrashed($this->uuid(), $user_id, $id));

        return $this;
    }

    public function restoreReminder(string $user_id, string $id)
    {
        $this->recordThat(new ReminderRestored($this->uuid(), $user_id, $id));

        return $this;
    }

    public function deleteReminder(string $user_id, string $id)
    {
        $this->recordThat(new ReminderDeleted($this->uuid(), $user_id, $id));

        return $this;
    }
}
