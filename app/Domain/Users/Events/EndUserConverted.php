<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserConverted extends GymRevCrudEvent
{
    public const OPERATION = 'CONVERTED';

    public string $member;
    public function __construct(string $member)
    {
        parent::__construct();
        $this->member = $member;
    }

    public function getEntity(): string
    {
        return EndUser::class;
    }

    protected function getOperation(): string
    {
        return self::OPERATION;
    }
}
