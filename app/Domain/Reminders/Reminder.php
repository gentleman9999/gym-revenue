<?php

namespace App\Domain\Reminders;

use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Models\User;
use App\Models\Calendar\CalendarEvent;
use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reminder extends GymRevProjection
{
    use HasFactory;

    protected $fillable = ['entity_type', 'entity_id', 'user_id', 'name', 'description', 'remind_time', 'triggered_at'];

    public function event(): HasOne
    {
        return $this->hasOne(CalendarEvent::class, 'id', 'entity_id');
    }

    public function user(): HasOne
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('user_id', '=', $search);
            });
        });
    }
}
