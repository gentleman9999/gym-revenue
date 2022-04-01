<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Classifications\ClassificationCreated;
use App\StorableEvents\Clients\Classifications\ClassificationDeleted;
use App\StorableEvents\Clients\Classifications\ClassificationRestored;
use App\StorableEvents\Clients\Classifications\ClassificationTrashed;
use App\StorableEvents\Clients\Classifications\ClassificationUpdated;

trait ClientClassificationActions
{

    public function createClassification(string $user_id, array $payload)
    {
        $this->recordThat(new ClassificationCreated($this->uuid(), $user_id, $payload));
        return $this;
    }

    public function updateClassification(string $user_id, array $payload)
    {
        $this->recordThat(new ClassificationUpdated($this->uuid(), $user_id, $payload));
        return $this;
    }

    public function trashClassification(string $user_id, string $id)
    {
        $this->recordThat(new ClassificationTrashed($this->uuid(), $user_id, $id));
        return $this;
    }

    public function restoreClassification(string $user_id, string $id)
    {
        $this->recordThat(new ClassificationRestored($this->uuid(), $user_id, $id));
        return $this;
    }

    public function deleteClassification(string $user_id, string $id)
    {
        $this->recordThat(new ClassificationDeleted($this->uuid(), $user_id, $id));
        return $this;
    }
}
