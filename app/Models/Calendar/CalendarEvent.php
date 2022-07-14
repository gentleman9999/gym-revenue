<?php

namespace App\Models\Calendar;

use App\Domain\Clients\Models\Client;
use App\Domain\Users\Models\User;
use App\Models\File;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Location
 *
 * @mixin Builder
 */
class CalendarEvent extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'client_id', 'title', 'description', 'full_day_event', 'start', 'end', 'color', 'event_type_id', 'owner_id', 'event_completion', 'location_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function type()
    {
        return $this->hasOne(CalendarEventType::class, 'id', 'event_type_id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function attendees()
    {
        return $this->hasMany(CalendarAttendee::class, 'calendar_event_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'entity_id', 'id')->where('entity_type', CalendarEvent::class);
    }

    public function fixDate($data): array
    {
        $fixedArray = [];
        foreach ($data as $item) {
            $fixedArray[] = substr(str_replace('T', ' ', $item), 0, 19);
        }

        return $fixedArray;
    }

    /** Event Scoping with filters */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            });
        })->when($filters['start'] ?? null, function ($query) use ($filters) {
            $end = $filters['end'] ?? Carbon::parse($filters['start'], 'yyyy-mm-dd')->addDays(1);//add one day
            $query->whereBetween('start', $this->fixDate([$filters['start'],$end]));
        })->when($filters['viewUser'] ?? null, function ($query) use ($filters) {
            $query->where(function ($query) use ($filters) {
                $query->where('attendees', 'like', '%"id": ' . $filters['viewUser'] . ',%');
            });
        });
    }
}
