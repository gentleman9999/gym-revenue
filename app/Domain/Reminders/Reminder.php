<?php

namespace App\Domain\Reminders;

use App\Domain\Users\Models\User;
use App\Models\Calendar\CalendarEvent;
use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends GymRevProjection
{
    use HasFactory;

    protected $fillable = ['entity_type', 'entity_id', 'user_id', 'name', 'description', 'remind_time', 'triggered_at'];

    public function event()
    {
        return $this->hasOne(CalendarEvent::class, 'id', 'entity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
