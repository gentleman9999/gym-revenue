<?php

namespace App\Aggregates\Clients;

use App\StorableEvents\Clients\Files\FileCreated;
use App\StorableEvents\Clients\Files\FileDeleted;
use App\StorableEvents\Clients\Files\FileRestored;
use App\StorableEvents\Clients\Files\FileTrashed;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class FileAggregate extends AggregateRoot
{

    protected string $default_team = '';
    protected array $teams = [];

    protected static bool $allowConcurrency = true;

    public function createFile(string $userId, string $tmpKey, $clientId)
    {
        $this->recordThat(new FileCreated($userId, $tmpKey ,$this->uuid()));
        return $this;
    }

    public function trashFile(string $userId)
    {
        $this->recordThat(new FileTrashed($userId));
        return $this;
    }

    public function restoreFile(string $userId)
    {
        $this->recordThat(new FileRestored($userId));
        return $this;
    }
    public function deleteFile(string $userId, $key)
    {
        $this->recordThat(new FileDeleted($userId, $key));
        return $this;
    }
}
