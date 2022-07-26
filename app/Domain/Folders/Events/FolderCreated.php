<?php

namespace App\Domain\Folders\Events;

use App\Models\Folder;
use App\StorableEvents\EntityCreated;

class FolderCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return Folder::class;
    }
}
