<?php

declare(strict_types=1);

namespace App\Domain\Folders\Events;

use App\Models\Folder;
use App\StorableEvents\EntityDeleted;

class FolderDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Folder::class;
    }
}
