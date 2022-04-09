<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarAttendee extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'entity_type', 'entity_id', 'entity_data', 'calendar_event_id', 'invitation_status'];

    protected $casts = ['entity_data' => 'array'];

}
