<?php

namespace App\Domain\Teams\Folders;

use App\Models\Folder;
use App\StorableEvents\EntityCreated;

class FolderCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return Folder::class;
    }
}
