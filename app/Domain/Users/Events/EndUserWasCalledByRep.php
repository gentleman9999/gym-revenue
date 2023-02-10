<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserWasCalledByRep extends GymRevCrudEvent
{
    public const OPERATION = 'CALLED';
    public array $payload;

    public function __construct(array $payload)
    {
        parent::__construct();
        $this->payload = $payload;
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
