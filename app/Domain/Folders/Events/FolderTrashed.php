<?php

declare(strict_types=1);

namespace App\Domain\Folders\Events;

use App\Models\Folder;
use App\StorableEvents\EntityDeleted;

class FolderTrashed extends EntityDeleted
{
    public function getEntity(): string
    {
        return Folder::class;
    }
}
