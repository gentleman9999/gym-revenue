<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories\Events;

use App\Domain\EntrySourceCategories\EntrySourceCategory;
use App\StorableEvents\EntityCreated;

class EntrySourceCategoryTrashed extends EntityCreated
{
    public function getEntity(): string
    {
        return EntrySourceCategory::class;
    }
}
