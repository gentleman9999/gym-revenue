<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories\Events;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\StorableEvents\EntityCreated;

class AgreementCategoryRestored extends EntityCreated
{
    public function getEntity(): string
    {
        return AgreementCategory::class;
    }
}
