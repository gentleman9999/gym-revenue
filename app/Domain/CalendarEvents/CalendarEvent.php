<?php

declare(strict_types=1);

namespace App\Domain\CalendarEvents;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Clients\Projections\Client;
use App\Domain\Notifications\Validations\NotifiableInterface;
use App\Domain\Notifications\Validations\NotificationValidationException;
use App\Domain\Users\Models\User;
use App\Models\Calendar\Carbon;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Location
 *
 * @mixin Builder
 */
class CalendarEvent extends GymRevProjection implements NotifiableInterface
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;


    /** @var array<string> */
    protected $fillable = [
        'title',
        'description',
        'full_day_event',
        'start',
        'end',
        'color',
        'event_type_id',
        'owner_id',
        'event_completion',
        'location_id',
        'editable',
        'call_task',
        'overdue_reminder_sent',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function type(): HasOne
    {
        return $this->hasOne(CalendarEventType::class, 'id', 'event_type_id');
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(CalendarAttendee::class, 'calendar_event_id', 'id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany('App\Models\File', 'fileable');
    }

    public function fixDate($data): array
    {
        $fixedArray = [];
        foreach ($data as $item) {
            $fixedArray[] = substr(str_replace('T', ' ', $item), 0, 19);
        }

        return $fixedArray;
    }

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('title', 'like', '%' . $search . '%');
            });
        })->when($filters['start'] ?? null, function ($query) use ($filters): void {
            $end = $filters['end'] ?? Carbon::parse($filters['start'], 'yyyy-mm-dd')->addDays(1);//add one day
            $query->whereBetween('start', $this->fixDate([$filters['start'], $end]));
        })->when($filters['viewUser'] ?? null, function ($query) use ($filters): void {
            $query->whereHas('attendees', function ($query) use ($filters): void {
                $query->where('entity_data', 'like', '%"id": ' . $filters['viewUser'] . ',%');
            });
        });
    }

    public function entityDataValidation(array $data): void
    {
        if (! isset($data['entity']['start'], $data['entity']['title'])) {
            throw new NotificationValidationException('entity should be of format: ["entity" => ["start" => "2022-01-01", "title" => "foo"]');
        }
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
