<?php

namespace App\Domain\Departments;

use App\Models\Position;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use Sortable;
    use SoftDeletes;

    protected $fillable = ['client_id', 'name'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'department_position', 'department_id', 'position_id');
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
