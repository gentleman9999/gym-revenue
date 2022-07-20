<?php

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\GymRevCrudEvent;

class TeamMemberAdded extends GymRevCrudEvent
{
    public $email;

    public function __construct(string $email)
    {
        parent::__construct();
        $this->email = $email;
    }

    protected function getEntity(): string
    {
        return Team::class;
    }

    protected function getOperation(): string
    {
        return "ADDED";
    }
}