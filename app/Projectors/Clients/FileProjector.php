<?php

namespace App\Projectors\Clients;

use App\StorableEvents\Clients\Files\FileCreated;
use App\StorableEvents\Clients\Files\FileDeleted;
use App\StorableEvents\Clients\Files\FileRenamed;
use App\StorableEvents\Clients\Files\FileRestored;
use App\StorableEvents\Clients\Files\FileTrashed;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class FileProjector extends Projector
{
    public function onFileCreated(FileCreated $event)
    {
    }

    public function onFileRenamed(FileRenamed $event)
    {

    }

    public function onFileTrashed(FileTrashed $event)
    {

    }

    public function onFileRestored(FileRestored $event)
    {

    }

    public function onFileDeleted(FileDeleted $event)
    {

    }
}
