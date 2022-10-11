<?php

namespace App\Domain\Chat\Aggregates;

use App\Domain\Chat\Events\ChatCreated;
use App\Domain\Chat\Events\ChatDeleted;
use App\Domain\Chat\Events\ChatRestored;
use App\Domain\Chat\Events\ChatUpdated;
use App\Domain\Chat\Models\Chat;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ChatAggregate extends AggregateRoot
{
    protected string $client_id;
    protected array $data;

    public function create(array $payload): static
    {
        $this->recordThat(new ChatCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new ChatUpdated($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new ChatDeleted());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new ChatRestored());

        return $this;
    }

    public function applyOnChatCreated(ChatCreated $event): void
    {
        $data = array_filter_only_fillable($event->payload, Chat::class);
        $this->client_id = $event->payload['client_id'] ?? null;
        $this->data = $data;
    }

    public function applyOnChatUpdated(ChatUpdated $event): void
    {
//        $data = array_filter_only_fillable($event->payload, Chat::class);
//        $this->client_id = $event->payload['client_id'] ?? null;
//        $this->data = $data;
    }

    public function applyOnChatDeleted(ChatDeleted $event): void
    {
//        $data = array_filter_only_fillable($event->payload, Chat::class);
//        $this->client_id = $event->payload['client_id'] ?? null;
//        $this->data = $data;
    }

    public function applyOnChatRestored(ChatRestored $event): void
    {
//        $data = array_filter_only_fillable($event->payload, Chat::class);
//        $this->client_id = $event->payload['client_id'] ?? null;
//        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getClientId(): ?string
    {
        return $this->client_id;
    }
}
