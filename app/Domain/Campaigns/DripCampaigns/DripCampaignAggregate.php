<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns;

use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignCompleted;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignCreated;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDeleted;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignLaunched;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignPublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignRestored;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignTrashed;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignUnpublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignUpdated;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeCompleted;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeLaunched;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBePublished;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeUnpublished;
use Carbon\CarbonImmutable;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class DripCampaignAggregate extends AggregateRoot
{
    protected CampaignStatusEnum $status;
    protected string $client_id;
    protected array $data;

    public function __construct()
    {
        $this->status = CampaignStatusEnum::DRAFT;
    }

    public function create(array $payload): static
    {
        $payload['status'] = $this->status;
        $this->recordThat(new DripCampaignCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new DripCampaignTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new DripCampaignRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new DripCampaignDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        if (array_key_exists('send_at', $this->data)) {
            $send_at = CarbonImmutable::create($this->data['send_at']);


            if (! $this->isDraft() && $send_at <= CarbonImmutable::now()->addMinutes(-1)) {
                //campaign is not a draft and should have already been started.
                //only allow updating name
                $payload = array_filter_only_keys($payload, ['name']);
            }
        }
        $this->recordThat(new DripCampaignUpdated($payload));

        return $this;
    }

    /**
     * Publishes the campaign (set to status to PENDING).
     * @return $this
     * @throws CampaignCouldNotBePublished
     */
    public function publish(): static
    {
        if (! $this->isDraft()) {
            $expected = CampaignStatusEnum::DRAFT->name;
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBePublished($msg);
        }

        $this->recordThat(new DripCampaignPublished());

        return $this;
    }

    /**
     * Unpublishes the campaign (set to status to DRAFT).
     * @return $this
     * @throws CampaignCouldNotBeUnpublished
     */
    public function unpublish(): static
    {
        if (! $this->isPending()) {
            $expected = CampaignStatusEnum::PENDING->name;
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeUnpublished($msg);
        }

        $this->recordThat(new DripCampaignUnpublished());

        return $this;
    }

    /**
     * Launches the campaign.
     * @return $this
     * @throws CampaignCouldNotBeLaunched
     */
    public function launch(): static
    {
        if (! $this->isPending()) {
            $expected = CampaignStatusEnum::PENDING->name;
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeLaunched($msg);
        }

        $this->recordThat(new DripCampaignLaunched());

        return $this;
    }

    /**
     * Completes the campaign.
     * @return $this
     * @throws CampaignCouldNotBeCompleted
     */
    public function complete(): static
    {
        if (! $this->isActive()) {
            $expected = CampaignStatusEnum::ACTIVE->name;
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeCompleted($msg);
        }
        $this->recordThat(new DripCampaignCompleted());

        return $this;
    }

    public function applyOnDripCampaignCreated(DripCampaignCreated $event): void
    {
        $data = array_filter_only_fillable($event->payload, DripCampaign::class);
        if (array_key_exists('client_id', $event->payload)) {
            $this->client_id = $event->payload['client_id'];
        }
        $this->data = $data;
    }

    public function applyOnDripCampaignUpdated(DripCampaignUpdated $event): void
    {
        $this->data = array_merge(array_filter_only_fillable($this->data, DripCampaign::class), $event->payload);
    }

    public function applyOnDripCampaignPublished(DripCampaignPublished $event): void
    {
        $this->status = CampaignStatusEnum::PENDING;
    }

    public function applyOnDripCampaignUnpublished(DripCampaignUnpublished $event): void
    {
        $this->status = CampaignStatusEnum::DRAFT;
    }

    public function applyOnDripCampaignLaunched(DripCampaignLaunched $event): void
    {
        $this->status = CampaignStatusEnum::ACTIVE;
    }

    public function applyOnDripCampaignCompleted(DripCampaignCompleted $event): void
    {
        $this->status = CampaignStatusEnum::COMPLETED;
    }

    public function isDraft(): bool
    {
        return $this->status === CampaignStatusEnum::DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === CampaignStatusEnum::PENDING;
    }

    public function isActive(): bool
    {
        return $this->status === CampaignStatusEnum::ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === CampaignStatusEnum::COMPLETED;
    }

    public function getStatus(): CampaignStatusEnum
    {
        return $this->status;
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
