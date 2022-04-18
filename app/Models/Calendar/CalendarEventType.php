<?php

namespace App\Models\Calendar;

use App\Models\Clients\Client;
use App\Models\Traits\Sortable;
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
class CalendarEventType extends Model
{
    use Notifiable, SoftDeletes, Uuid, HasFactory, Sortable;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'client_id', 'name', 'description', 'color', 'type'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /** Event Scoping with filters */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('description', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });;
    }
}
