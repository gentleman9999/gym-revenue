<?php

namespace App\Domain\AgreementTemplates\Events;

use App\Domain\Agreements\Projections\AgreementTemplate;
use App\StorableEvents\EntityCreated;

class AgreementTemplateCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return AgreementTemplate::class;
    }
}
