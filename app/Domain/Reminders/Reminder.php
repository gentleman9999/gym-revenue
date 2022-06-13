<?php

namespace App\Domain\Reminders;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = ['id', 'entity_type', 'entity_id', 'user_id', 'name', 'description', 'remind_time', 'triggered_at'];

    public function event()
    {
        return $this->hasOne('App\Models\Calendar\CalendarEvent', 'id', 'entity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
