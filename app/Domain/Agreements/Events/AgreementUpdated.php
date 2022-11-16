<?php

namespace App\Domain\Agreements\Events;

use App\Domain\Agreements\Projections\Agreement;
use App\StorableEvents\EntityUpdated;

class AgreementUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Agreement::class;
    }
}
