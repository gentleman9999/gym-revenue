<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\Domain\EndUsers\Projections\EndUser;
use App\StorableEvents\GymRevCrudEvent;

class EndUserWasTextMessagedByRep extends GymRevCrudEvent
{
    public const OPERATION = 'TEXTED';
    public array $payload;

    public function __construct(array $payload)
    {
        parent::__construct();
        $this->payload = $payload;
    }

    protected function getOperation(): string
    {
        return self::OPERATION;
    }

    public function getEntity(): string
    {
        return EndUser::class;
    }
}
