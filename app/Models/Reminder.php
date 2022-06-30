<?php

namespace App\Models;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Location
 *
 * @mixin Builder
 */

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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('user_id', '=', $search);
            });
        });
    }
}
