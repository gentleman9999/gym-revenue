<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserConverted extends GymRevCrudEvent
{
    public const OPERATION = 'CONVERTED';

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
        return self::OPERATION;
    }
}
