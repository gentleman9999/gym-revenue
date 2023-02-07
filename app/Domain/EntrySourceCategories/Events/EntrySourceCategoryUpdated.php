<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories\Events;

use App\Domain\EntrySourceCategories\EntrySourceCategory;
use App\StorableEvents\EntityUpdated;

class EntrySourceCategoryUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return EntrySourceCategory::class;
    }
}
