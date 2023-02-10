<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\Enums;

enum CampaignStatusEnum: string
{
    /**
     * No communications will be sent out while a campaign is in Draft status
     */
    case DRAFT = "DRAFT";
    /**
     * Indicates that this campaign is just waiting for it's scheduled time to start
     */
    case PENDING = "PENDING";
    /**
     * Indicates that this campaign is currently active and sending communications
     */
    case ACTIVE = "ACTIVE";
    /**
     * Indicates that this campaign has completely finished executing.
     */
    case COMPLETED = "COMPLETED";
}
