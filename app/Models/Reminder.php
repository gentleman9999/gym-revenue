<?php

namespace App\Models;

use App\Models\Calendar\CalendarEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = ['id', 'entity_type', 'entity_id', 'user_id', 'name', 'description', 'remind_time', 'triggered_at'];

    public function events()
    {
        return $this->hasMany('App\Models\Calendar\CalendarEvent', 'id', 'entity_id')->where('entity_type', CalendarEvent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
