<?php

declare(strict_types=1);

namespace App\Domain\EngagementEvents\Models;

use App\Models\GymRevProjection;

class EngagementEvents extends GymRevProjection
{
    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $table = 'engagement_events';

    /** @var array<string> */
    protected $fillable = ['id', 'entity', 'operation', 'stored-event-id', 'aggregate_uuid'];
}
