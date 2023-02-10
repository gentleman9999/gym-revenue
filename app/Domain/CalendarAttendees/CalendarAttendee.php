<?php

declare(strict_types=1);

namespace App\Domain\CalendarAttendees;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarAttendee extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<string> */
    protected $fillable = ['entity_type', 'entity_id', 'entity_data', 'calendar_event_id', 'invitation_status'];

    /** @var array<string, string> */
    protected $casts = ['entity_data' => 'array'];

    public function event(): HasOne
    {
        return $this->hasOne(CalendarEvent::class, 'id', 'calendar_event_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
