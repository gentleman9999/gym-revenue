<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\EntityTrashed;

abstract class EndUserTrashed extends EntityTrashed
{
    public string $reason;

    public function __construct(string $reason)
    {
        parent::__construct();
        $this->reason = $reason;
    }
}
