<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Events;

use App\Domain\Reminders\Reminder;
use App\StorableEvents\EntityDeleted;

class ReminderDeleted extends EntityDeleted
{
    public string $id;

    public function __construct(string $id)
    {
        parent::__construct();
        $this->id = $id;
    }

    public function getEntity(): string
    {
        return Reminder::class;
    }
}
