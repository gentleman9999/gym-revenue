<?php

namespace App\Domain\Agreements\Events;

use App\Domain\Agreements\Projections\Agreement;
use App\StorableEvents\EntityDeleted;

class AgreementDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Agreement::class;
    }
}
