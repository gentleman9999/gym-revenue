<?php

declare(strict_types=1);

namespace App\Domain\EngagementEvents\Models;

use App\Models\GymRevProjection;

class EngagementEvents extends GymRevProjection
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'engagement_events';

    public $incrementing = false;

    protected $fillable = ['id', 'entity', 'operation', 'stored-event-id', 'aggregate_uuid'];
}
