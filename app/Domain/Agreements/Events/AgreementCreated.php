<?php

namespace App\Domain\Agreements\Events;

use App\Domain\Agreements\Projections\Agreement;
use App\StorableEvents\EntityCreated;

class AgreementCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Agreement::class;
    }
}
