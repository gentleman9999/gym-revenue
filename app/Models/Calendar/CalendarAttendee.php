<?php

namespace App\Models\Calendar;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarAttendee extends Model
{
    use HasFactory, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = ['id', 'entity_type', 'entity_id', 'entity_data', 'calendar_event_id', 'invitation_status'];

    protected $casts = ['entity_data' => 'array'];

    public function event()
    {
        return $this->hasOne('App\Models\Calendar\CalendarEvent', 'id', 'calendar_event_id');
    }

}
