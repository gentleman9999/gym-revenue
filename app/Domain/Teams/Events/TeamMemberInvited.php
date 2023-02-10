<?php

declare(strict_types=1);

namespace App\Domain\Teams\Events;

use App\Domain\Teams\Models\Team;
use App\StorableEvents\GymRevCrudEvent;

class TeamMemberInvited extends GymRevCrudEvent
{
    public $email;

    public function __construct(string $email)
    {
        parent::__construct();
        $this->email = $email;
    }

    public function getEntity(): string
    {
        return Team::class;
    }

    protected function getOperation(): string
    {
        return "INVITED";
    }
}
