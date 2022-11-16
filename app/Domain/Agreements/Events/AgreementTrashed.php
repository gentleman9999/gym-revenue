<?php

namespace App\Domain\Agreements\Events;

use App\Domain\Agreements\Projections\Agreement;
use App\StorableEvents\EntityTrashed;

class AgreementTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Agreement::class;
    }
}
