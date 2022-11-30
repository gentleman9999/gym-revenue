<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserConverted extends GymRevCrudEvent
{
    public function getEntity(): string
    {
        return EndUser::class;
    }
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
