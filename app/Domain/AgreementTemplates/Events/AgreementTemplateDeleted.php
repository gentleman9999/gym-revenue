<?php

declare(strict_types=1);

namespace App\Domain\AgreementTemplates\Events;

use App\Domain\Agreements\Projections\AgreementTemplate;
use App\StorableEvents\EntityDeleted;

class AgreementTemplateDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return AgreementTemplate::class;
    }
}
