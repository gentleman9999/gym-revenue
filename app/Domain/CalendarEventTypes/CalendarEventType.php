<?php

declare(strict_types=1);

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

    /** @var array<string>  */
    protected $fillable = ['name', 'description', 'color', 'type'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('description', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['trashed'] ?? null, function ($query, $trashed): void {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
