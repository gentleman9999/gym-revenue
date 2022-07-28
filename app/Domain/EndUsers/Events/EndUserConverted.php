<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

abstract class EndUserConverted extends GymRevCrudEvent
{
    public string $member;

    public function __construct(string $member)
    {
        parent::__construct();
        $this->member = $member;
    }

    protected function getOperation(): string
    {
        return "CONVERTED";
    }
}
