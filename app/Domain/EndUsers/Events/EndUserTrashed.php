<?php

namespace App\Domain\EndUsers\Events;

use App\Domain\EndUsers\Projections\EndUser;
use App\StorableEvents\EntityTrashed;

class EndUserTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return EndUser::class;
    }

    public string $reason;

    public function __construct(string $reason)
    {
        parent::__construct();
        $this->reason = $reason;
    }
}
