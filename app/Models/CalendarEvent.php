<?php

namespace App\Models;

use App\Models\Clients\Client;
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
    use Notifiable, SoftDeletes, Uuid, HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'client_id', 'title', 'description', 'full_day_event', 'start', 'end', 'color', 'event_type_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function type()
    {
        return $this->hasOne('App\Models\CalendarEventType', 'id', 'event_type_id');
    }

    public function fixDate($data): array
    {
        $fixedArray = [];
        foreach ($data as $item)
        {
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
            $query->whereBetween('start', $this->fixDate([$filters['start'],$filters['end']]));
        });

    }
}
