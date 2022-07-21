<?php

namespace App\Domain\Folders\Events;

use App\Models\Folder;
use App\StorableEvents\EntityDeleted;

class FolderDeleted extends EntityDeleted
{
    protected function getEntity(): string
    {
        return Folder::class;
    }
}
