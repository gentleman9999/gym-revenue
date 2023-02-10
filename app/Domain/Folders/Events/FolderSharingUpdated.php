<?php

declare(strict_types=1);

namespace App\Domain\Folders\Events;

use App\Models\Folder;
use App\StorableEvents\EntityCreated;

class FolderSharingUpdated extends EntityCreated
{
    public function getEntity(): string
    {
        return Folder::class;
    }
}
