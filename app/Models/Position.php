<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Departments\Department;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory;
    use Sortable;
    use SoftDeletes;

    /** @var bool */
    public $incrementing = false;

    /** @var array<string> */
    protected $fillable = ['name'];

    /** @var string */
    protected $keyType = 'string';

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_position', 'position_id', 'department_id');
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
        })->when($filters['trashed'] ?? null, function ($query, $trashed): void {
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
