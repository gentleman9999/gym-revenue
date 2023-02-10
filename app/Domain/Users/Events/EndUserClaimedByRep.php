<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\Domain\Users\Models\EndUser;
use App\StorableEvents\GymRevCrudEvent;

class EndUserClaimedByRep extends GymRevCrudEvent
{
    public const OPERATION = 'CLAIMED';

    public string $claimed_by_user_id;

    public function __construct(string $claimed_by_user_id)
    {
        parent::__construct();
        $this->claimed_by_user_id = $claimed_by_user_id;
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
