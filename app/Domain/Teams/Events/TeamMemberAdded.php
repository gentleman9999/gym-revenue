<?php

declare(strict_types=1);

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\GymRevCrudEvent;

class TeamMemberAdded extends GymRevCrudEvent
{
    public function __construct(public ?string $user_id)
    {
        parent::__construct();
    }

    public function getEntity(): string
    {
        return Team::class;
    }

    protected function getOperation(): string
    {
        return "ADDED";
    }
}
