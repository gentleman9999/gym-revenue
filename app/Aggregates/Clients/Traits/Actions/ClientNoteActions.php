<?php

declare(strict_types=1);

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Notes\NoteCreated;
use App\StorableEvents\Clients\Notes\NoteDeleted;
use App\StorableEvents\Clients\Notes\NoteRestored;
use App\StorableEvents\Clients\Notes\NoteTrashed;
use App\StorableEvents\Clients\Notes\NoteUpdated;

trait ClientNoteActions
{
    public function createNote(string $user_id, array $payload)
    {
        $this->recordThat(new NoteCreated($this->uuid(), $user_id, $payload));

        return $this;
    }

    public function updateNote(string $user_id, array $payload)
    {
        $this->recordThat(new NoteUpdated($this->uuid(), $user_id, $payload));

        return $this;
    }

    public function trashNote(string $user_id, string $id)
    {
        $this->recordThat(new NoteTrashed($this->uuid(), $user_id, $id));

        return $this;
    }

    public function restoreNote(string $user_id, string $id)
    {
        $this->recordThat(new NoteRestored($this->uuid(), $user_id, $id));

        return $this;
    }

    public function deleteNote(string $user_id, string $id)
    {
        $this->recordThat(new NoteDeleted($this->uuid(), $user_id, $id));

        return $this;
    }
}
