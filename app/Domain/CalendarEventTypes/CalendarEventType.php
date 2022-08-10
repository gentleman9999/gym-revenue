<?php

namespace App\Domain\CalendarEventTypes;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Location
 *
 * @mixin Builder
 */
class CalendarEventType extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Sortable;

    protected $fillable = ['name', 'description', 'color', 'type'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** Event Scoping with filters */
    public function scopeFilter($query, array $filters): void
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
        });
        ;
    }
}
